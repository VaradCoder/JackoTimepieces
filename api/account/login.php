<?php
/**
 * Login API Endpoint for JackoTimespiece
 * Handles user authentication and session management
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
    $requiredFields = ['email', 'password'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            throw new Exception(ucfirst($field) . ' is required');
        }
    }
    
    // Validate email format
    $emailValidation = validateEmail($input['email']);
    if (!$emailValidation['valid']) {
        throw new Exception($emailValidation['error']);
    }
    
    // Sanitize input
    $email = sanitizeInput($input['email']);
    $password = $input['password']; // Don't sanitize password for verification
    
    // Connect to database
    $conn = getConnection();
    
    // Attempt authentication
    $authResult = authenticateUser($email, $password, $conn);
    
    if (!$authResult['success']) {
        throw new Exception($authResult['error']);
    }
    
    // Log successful login
    logActivity($authResult['user']['id'], 'login', 'User logged in successfully', $conn);
    
    // Prepare response
    $response = [
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $authResult['user']['id'],
            'username' => $authResult['user']['username'],
            'email' => $authResult['user']['email'],
            'first_name' => $authResult['user']['first_name'],
            'last_name' => $authResult['user']['last_name'],
            'role' => $authResult['user']['role'],
            'image' => $authResult['user']['image']
        ],
        'redirect_url' => $authResult['user']['role'] === 'admin' ? '../admin/index.php' : '../public/account/index.php'
    ];
    
    // Check if there's a redirect URL stored in session
    if (isset($_SESSION['redirect_after_login'])) {
        $response['redirect_url'] = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']);
    }
    
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