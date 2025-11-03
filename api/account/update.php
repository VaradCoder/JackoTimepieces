<?php
/**
 * Account Update API Endpoint for JackoTimespiece
 * Handles user profile updates and account modifications
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../core/config/app.php';
require_once '../../core/db/connection.php';
require_once '../../core/helpers/auth.php';
require_once '../../core/helpers/validation.php';
require_once '../../core/middleware/auth-customer.php';

// Only allow POST and PUT requests
if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'])) {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Require authentication
requireCustomerAuth();

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Determine update type
    $updateType = $input['update_type'] ?? 'profile';
    
    switch ($updateType) {
        case 'profile':
            $result = updateProfile($input, $conn);
            break;
        case 'password':
            $result = updatePassword($input, $conn);
            break;
        case 'profile_picture':
            $result = updateProfilePicture($input, $conn);
            break;
        default:
            throw new Exception('Invalid update type');
    }
    
    if (!$result['success']) {
        throw new Exception($result['error']);
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error'
    ]);
}

/**
 * Update user profile information
 */
function updateProfile($input, $conn) {
    $userId = getCurrentUserId();
    
    // Validate required fields
    $requiredFields = ['first_name', 'last_name'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            return ['success' => false, 'error' => ucfirst(str_replace('_', ' ', $field)) . ' is required'];
        }
    }
    
    // Validate names
    $firstNameValidation = validateName($input['first_name'], 'First name');
    if (!$firstNameValidation['valid']) {
        return ['success' => false, 'error' => $firstNameValidation['error']];
    }
    
    $lastNameValidation = validateName($input['last_name'], 'Last name');
    if (!$lastNameValidation['valid']) {
        return ['success' => false, 'error' => $lastNameValidation['error']];
    }
    
    // Validate optional phone number
    if (!empty($input['phone'])) {
        $phoneValidation = validatePhone($input['phone']);
        if (!$phoneValidation['valid']) {
            return ['success' => false, 'error' => $phoneValidation['error']];
        }
        $input['phone'] = $phoneValidation['phone'];
    }
    
    // Validate optional address fields
    if (!empty($input['address'])) {
        $addressValidation = validateAddress($input['address']);
        if (!$addressValidation['valid']) {
            return ['success' => false, 'error' => $addressValidation['error']];
        }
    }
    
    if (!empty($input['city'])) {
        $cityValidation = validateCity($input['city']);
        if (!$cityValidation['valid']) {
            return ['success' => false, 'error' => $cityValidation['error']];
        }
    }
    
    if (!empty($input['state'])) {
        $stateValidation = validateState($input['state']);
        if (!$stateValidation['valid']) {
            return ['success' => false, 'error' => $stateValidation['error']];
        }
    }
    
    if (!empty($input['zip_code'])) {
        $zipValidation = validateZipCode($input['zip_code']);
        if (!$zipValidation['valid']) {
            return ['success' => false, 'error' => $zipValidation['error']];
        }
        $input['zip_code'] = $zipValidation['zip_code'];
    }
    
    // Sanitize input
    $userData = [
        'first_name' => sanitizeInput($input['first_name']),
        'last_name' => sanitizeInput($input['last_name']),
        'phone' => sanitizeInput($input['phone'] ?? ''),
        'address' => sanitizeInput($input['address'] ?? ''),
        'city' => sanitizeInput($input['city'] ?? ''),
        'state' => sanitizeInput($input['state'] ?? ''),
        'zip_code' => sanitizeInput($input['zip_code'] ?? '')
    ];
    
    // Update profile
    $updateResult = updateUserProfile($userId, $userData, $conn);
    
    if (!$updateResult['success']) {
        return $updateResult;
    }
    
    // Log activity
    logActivity($userId, 'profile_update', 'Profile updated', $conn);
    
    return [
        'success' => true,
        'message' => 'Profile updated successfully',
        'user' => getCurrentUser()
    ];
}

/**
 * Update user password
 */
function updatePassword($input, $conn) {
    $userId = getCurrentUserId();
    
    // Validate required fields
    $requiredFields = ['current_password', 'new_password', 'confirm_password'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            return ['success' => false, 'error' => ucfirst(str_replace('_', ' ', $field)) . ' is required'];
        }
    }
    
    // Validate new password strength
    $passwordValidation = validatePassword($input['new_password']);
    if (!$passwordValidation['valid']) {
        return ['success' => false, 'error' => $passwordValidation['error']];
    }
    
    // Validate password confirmation
    $confirmValidation = validatePasswordConfirmation($input['new_password'], $input['confirm_password']);
    if (!$confirmValidation['valid']) {
        return ['success' => false, 'error' => $confirmValidation['error']];
    }
    
    // Change password
    $changeResult = changePassword($userId, $input['current_password'], $input['new_password'], $conn);
    
    if (!$changeResult['success']) {
        return $changeResult;
    }
    
    // Log activity
    logActivity($userId, 'password_change', 'Password changed', $conn);
    
    return [
        'success' => true,
        'message' => 'Password changed successfully'
    ];
}

/**
 * Update profile picture
 */
function updateProfilePicture($input, $conn) {
    $userId = getCurrentUserId();
    
    // Check if file was uploaded
    if (!isset($_FILES['profile_picture'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }
    
    $file = $_FILES['profile_picture'];
    
    // Validate file upload
    $fileValidation = validateFileUpload($file, ['jpg', 'jpeg', 'png', 'gif'], 5 * 1024 * 1024);
    if (!$fileValidation['valid']) {
        return ['success' => false, 'error' => $fileValidation['error']];
    }
    
    // Validate image dimensions
    $dimensionValidation = validateImageDimensions($file, 100, 100, 2000, 2000);
    if (!$dimensionValidation['valid']) {
        return ['success' => false, 'error' => $dimensionValidation['error']];
    }
    
    // Upload and update profile picture
    $uploadResult = updateProfileImage($userId, $file, $conn);
    
    if (!$uploadResult['success']) {
        return $uploadResult;
    }
    
    // Log activity
    logActivity($userId, 'profile_picture_update', 'Profile picture updated', $conn);
    
    return [
        'success' => true,
        'message' => 'Profile picture updated successfully',
        'filename' => $uploadResult['filename']
    ];
}
?> 