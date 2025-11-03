<?php
/**
 * Remove from Cart API Endpoint for JackoTimespiece
 * Handles removing items from shopping cart
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../core/config/app.php';
require_once '../../core/db/connection.php';
require_once '../../core/helpers/cart.php';

// Only allow POST and DELETE requests
if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE'])) {
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
    if (empty($input['watch_id'])) {
        throw new Exception('Watch ID is required');
    }
    
    // Validate watch ID
    if (!is_numeric($input['watch_id']) || $input['watch_id'] <= 0) {
        throw new Exception('Invalid watch ID');
    }
    
    // Sanitize input
    $watchId = intval($input['watch_id']);
    
    // Connect to database
    $conn = getConnection();
    
    // Check if watch exists
    $stmt = $conn->prepare('SELECT id, name FROM watches WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $watchId);
    $stmt->execute();
    $watch = $stmt->get_result()->fetch_assoc();
    
    if (!$watch) {
        throw new Exception('Watch not found');
    }
    
    // Remove from cart
    $cartResult = removeFromCart($watchId);
    
    if (!$cartResult['success']) {
        throw new Exception($cartResult['error']);
    }
    
    // Get updated cart summary
    $cartSummary = getCartSummary($conn);
    
    // Log activity if user is logged in
    if (isset($_SESSION['user'])) {
        require_once '../../core/helpers/utils.php';
        logActivity($_SESSION['user']['id'], 'remove_from_cart', "Removed {$watch['name']} from cart", $conn);
    }
    
    // Prepare response
    $response = [
        'success' => true,
        'message' => $cartResult['message'],
        'cart_summary' => $cartSummary,
        'removed_item' => [
            'id' => $watch['id'],
            'name' => $watch['name']
        ]
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