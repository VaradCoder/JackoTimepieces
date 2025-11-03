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
    // Remove from wishlist
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND watch_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare wishlist delete query");
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
        
        echo json_encode([
            'success' => true,
            'message' => 'Product removed from wishlist successfully!',
            'wishlist_count' => $wishlist_count
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to remove product from wishlist']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
?> 