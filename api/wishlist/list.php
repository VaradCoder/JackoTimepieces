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

$user_id = $_SESSION['user']['id'];

try {
    // First, check if the watches table exists
    $result = $conn->query("SHOW TABLES LIKE 'watches'");
    if ($result->num_rows === 0) {
        // If watches table doesn't exist, just return wishlist items without watch details
        $stmt = $conn->prepare("SELECT watch_id FROM wishlist WHERE user_id = ? ORDER BY created_at DESC");
        if (!$stmt) {
            throw new Exception("Failed to prepare wishlist query");
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $wishlist_items = [];
        while ($row = $result->fetch_assoc()) {
            $wishlist_items[] = [
                'watch_id' => $row['watch_id'],
                'name' => 'Watch #' . $row['watch_id'],
                'price' => 0,
                'main_image' => 'default.jpg'
            ];
        }
    } else {
        // Watches table exists, use the full query
        $stmt = $conn->prepare("
            SELECT wl.watch_id, w.name, w.price, w.main_image 
            FROM wishlist wl 
            JOIN watches w ON wl.watch_id = w.id 
            WHERE wl.user_id = ? 
            ORDER BY wl.created_at DESC
        ");
        if (!$stmt) {
            throw new Exception("Failed to prepare wishlist query with watches join");
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $wishlist_items = [];
        while ($row = $result->fetch_assoc()) {
            $wishlist_items[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'wishlist_items' => $wishlist_items,
        'count' => count($wishlist_items)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
?> 