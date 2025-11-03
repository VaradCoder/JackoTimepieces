<?php
/**
 * Registration API Endpoint for JackoTimespiece
 * Handles new user registration and account creation
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../core/config/app.php';
require_once '../../core/db/connection.php';
require_once '../../core/helpers/auth.php';
require_once '../../core/helpers/validation.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}
try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    $requiredFields = ['username', 'email', 'password', 'confirm_password', 'first_name', 'last_name'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }
    
    // Validate email format
    $emailValidation = validateEmail($input['email']);
    if (!$emailValidation['valid']) {
        throw new Exception($emailValidation['error']);
    }
    
    // Validate password strength
    $passwordValidation = validatePassword($input['password']);
    if (!$passwordValidation['valid']) {
        throw new Exception($passwordValidation['error']);
    }
    
    // Validate password confirmation
    $confirmValidation = validatePasswordConfirmation($input['password'], $input['confirm_password']);
    if (!$confirmValidation['valid']) {
        throw new Exception($confirmValidation['error']);
    }
    
    // Validate username
    $usernameValidation = validateUsername($input['username']);
    if (!$usernameValidation['valid']) {
        throw new Exception($usernameValidation['error']);
    }
    
    // Validate names
    $firstNameValidation = validateName($input['first_name'], 'First name');
    if (!$firstNameValidation['valid']) {
        throw new Exception($firstNameValidation['error']);
    }
    
    $lastNameValidation = validateName($input['last_name'], 'Last name');
    if (!$lastNameValidation['valid']) {
        throw new Exception($lastNameValidation['error']);
    }
    
    // Validate optional phone number
    if (!empty($input['phone'])) {
        $phoneValidation = validatePhone($input['phone']);
        if (!$phoneValidation['valid']) {
            throw new Exception($phoneValidation['error']);
        }
        $input['phone'] = $phoneValidation['phone'];
    }
    
    // Validate optional address fields
    if (!empty($input['address'])) {
        $addressValidation = validateAddress($input['address']);
        if (!$addressValidation['valid']) {
            throw new Exception($addressValidation['error']);
        }
    }
    
    if (!empty($input['city'])) {
        $cityValidation = validateCity($input['city']);
        if (!$cityValidation['valid']) {
            throw new Exception($cityValidation['error']);
        }
    }
    
    if (!empty($input['state'])) {
        $stateValidation = validateState($input['state']);
        if (!$stateValidation['valid']) {
            throw new Exception($stateValidation['error']);
        }
    }
    
    if (!empty($input['zip_code'])) {
        $zipValidation = validateZipCode($input['zip_code']);
        if (!$zipValidation['valid']) {
            throw new Exception($zipValidation['error']);
        }
        $input['zip_code'] = $zipValidation['zip_code'];
    }
    
    // Sanitize input
    $userData = [
        'username' => sanitizeInput($input['username']),
        'email' => sanitizeInput($input['email']),
        'password' => $input['password'], // Don't sanitize password
        'first_name' => sanitizeInput($input['first_name']),
        'last_name' => sanitizeInput($input['last_name']),
        'phone' => sanitizeInput($input['phone'] ?? ''),
        'address' => sanitizeInput($input['address'] ?? ''),
        'city' => sanitizeInput($input['city'] ?? ''),
        'state' => sanitizeInput($input['state'] ?? ''),
        'zip_code' => sanitizeInput($input['zip_code'] ?? '')
    ];
    
    // Connect to database
    $conn = getConnection();
    
    // Attempt registration
    $registerResult = registerUser($userData, $conn);
    
    if (!$registerResult['success']) {
        throw new Exception($registerResult['error']);
    }
    
    // Log successful registration
    logActivity($registerResult['user']['id'], 'register', 'New user registered', $conn);
    
    // Prepare response
    $response = [
        'success' => true,
        'message' => 'Registration successful',
        'user' => [
            'id' => $registerResult['user']['id'],
            'username' => $registerResult['user']['username'],
            'email' => $registerResult['user']['email'],
            'first_name' => $registerResult['user']['first_name'],
            'last_name' => $registerResult['user']['last_name'],
            'role' => $registerResult['user']['role'],
            'image' => $registerResult['user']['image']
        ],
        'redirect_url' => '../public/account/index.php'
    ];
    
    echo json_encode($response);
    
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
?> 