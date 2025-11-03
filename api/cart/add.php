<?php
/**
 * Add to Cart API Endpoint for JackoTimespiece
 * Handles adding items to shopping cart with validation
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../core/config/app.php';
require_once '../../core/db/connection.php';
require_once '../../core/helpers/cart.php';
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
    if (empty($input['watch_id'])) {
        throw new Exception('Watch ID is required');
    }
    
    // Validate watch ID
    if (!is_numeric($input['watch_id']) || $input['watch_id'] <= 0) {
        throw new Exception('Invalid watch ID');
    }
    
    // Validate quantity
    $quantity = isset($input['quantity']) ? intval($input['quantity']) : 1;
    $quantityValidation = validateQuantity($quantity);
    if (!$quantityValidation['valid']) {
        throw new Exception($quantityValidation['error']);
    }
    
    // Sanitize input
    $watchId = intval($input['watch_id']);
    $quantity = $quantityValidation['quantity'];
    
    // Connect to database
    $conn = getConnection();
    
    // Check if watch exists and is active
    $stmt = $conn->prepare('SELECT id, name, price, stock_quantity, status FROM watches WHERE id = ? AND status = "active" LIMIT 1');
    $stmt->bind_param('i', $watchId);
    $stmt->execute();
    $watch = $stmt->get_result()->fetch_assoc();
    
    if (!$watch) {
        throw new Exception('Watch not found or not available');
    }
    
    // Check stock availability
    if ($watch['stock_quantity'] < $quantity) {
        throw new Exception('Insufficient stock. Available: ' . $watch['stock_quantity']);
    }
    
    // Add to cart
    $cartResult = addToCart($watchId, $quantity);
    
    if (!$cartResult['success']) {
        throw new Exception($cartResult['error']);
    }
    
    // Get updated cart summary
    $cartSummary = getCartSummary($conn);
    
    // Log activity if user is logged in
    if (isset($_SESSION['user'])) {
        require_once '../../core/helpers/utils.php';
        logActivity($_SESSION['user']['id'], 'add_to_cart', "Added {$quantity}x {$watch['name']} to cart", $conn);
    }
    
    // Prepare response
    $response = [
        'success' => true,
        'message' => $cartResult['message'],
        'cart_summary' => $cartSummary,
        'added_item' => [
            'id' => $watch['id'],
            'name' => $watch['name'],
            'price' => $watch['price'],
            'quantity' => $quantity,
            'subtotal' => $watch['price'] * $quantity
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