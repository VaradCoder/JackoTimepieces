<?php
session_start();
header('Content-Type: application/json');
require_once '../../core/db/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$user_id = $_SESSION['user']['id'];
$watch_id = intval($_POST['watch_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 1);

if ($watch_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
    exit;
}

try {
    // Check if product exists and is active
    $stmt = $conn->prepare("SELECT id, name, price, stock_quantity FROM watches WHERE id = ? AND is_active = 1");
    $stmt->bind_param("i", $watch_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    if (!$product) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Product not found']);
        exit;
    }
    
    // Check stock availability
    if ($product['stock_quantity'] < $quantity) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Insufficient stock']);
        exit;
    }
    
    // Check if item already exists in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND watch_id = ?");
    $stmt->bind_param("ii", $user_id, $watch_id);
    $stmt->execute();
    $existing_item = $stmt->get_result()->fetch_assoc();
    
    if ($existing_item) {
        // Update existing item
        $new_quantity = $existing_item['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("ii", $new_quantity, $existing_item['id']);
    } else {
        // Add new item
        $stmt = $conn->prepare("INSERT INTO cart_items (user_id, watch_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $watch_id, $quantity);
    }
    
    if ($stmt->execute()) {
        // Get updated cart count
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart_items WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $cart_count = $stmt->get_result()->fetch_assoc()['count'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart_count' => $cart_count,
            'product_name' => $product['name']
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to add product to cart']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
?> 