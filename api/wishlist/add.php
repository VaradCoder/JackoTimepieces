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

if ($watch_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
    exit;
}

try {
    // Check if watches table exists
    $result = $conn->query("SHOW TABLES LIKE 'watches'");
    if ($result->num_rows > 0) {
        // Check if product exists and is active
        $stmt = $conn->prepare("SELECT id, name FROM watches WHERE id = ? AND is_active = 1");
        if (!$stmt) {
            throw new Exception("Failed to prepare product check query");
        }
        $stmt->bind_param("i", $watch_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        
        if (!$product) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Product not found']);
            exit;
        }
    }
    
    // Check if already in wishlist
    $stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND watch_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare wishlist check query");
    }
    $stmt->bind_param("ii", $user_id, $watch_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Product is already in your wishlist']);
        exit;
    }
    
    // Add to wishlist
    $stmt = $conn->prepare("INSERT INTO wishlist (user_id, watch_id) VALUES (?, ?)");
    if (!$stmt) {
        throw new Exception("Failed to prepare wishlist insert query");
    }
    $stmt->bind_param("ii", $user_id, $watch_id);
    
    if ($stmt->execute()) {
        // Get updated wishlist count
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception("Failed to prepare count query");
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $wishlist_count = $stmt->get_result()->fetch_assoc()['count'];
        
        $product_name = isset($product) ? $product['name'] : "Watch #$watch_id";
        
        echo json_encode([
            'success' => true,
            'message' => 'Product added to wishlist successfully!',
            'wishlist_count' => $wishlist_count,
            'product_name' => $product_name
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to add product to wishlist']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
?> 