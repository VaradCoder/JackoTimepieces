<?php
/**
 * Watch Details API Endpoint for JackoTimespiece
 * Handles fetching detailed product information and related data
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../core/config/app.php';
require_once '../../core/db/connection.php';
require_once '../../core/helpers/utils.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Get watch ID from query parameters
    $watchId = $_GET['id'] ?? null;
    
    if (!$watchId) {
        throw new Exception('Watch ID is required');
    }
    
    // Validate watch ID
    if (!is_numeric($watchId) || $watchId <= 0) {
        throw new Exception('Invalid watch ID');
    }
    
    // Sanitize input
    $watchId = intval($watchId);
    
    // Connect to database
    $conn = getConnection();
    
    // Get watch details
    $stmt = $conn->prepare('
        SELECT w.*, c.name as category_name, c.slug as category_slug
        FROM watches w
        LEFT JOIN categories c ON w.category_id = c.id
        WHERE w.id = ? AND w.status = "active"
        LIMIT 1
    ');
    $stmt->bind_param('i', $watchId);
    $stmt->execute();
    $watch = $stmt->get_result()->fetch_assoc();
    
    if (!$watch) {
        throw new Exception('Watch not found or not available');
    }
    
    // Get watch images
    $stmt = $conn->prepare('SELECT * FROM watch_images WHERE watch_id = ? ORDER BY sort_order ASC');
    $stmt->bind_param('i', $watchId);
    $stmt->execute();
    $images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get watch specifications
    $stmt = $conn->prepare('SELECT * FROM watch_specifications WHERE watch_id = ? ORDER BY sort_order ASC');
    $stmt->bind_param('i', $watchId);
    $stmt->execute();
    $specifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get reviews
    $stmt = $conn->prepare('
        SELECT r.*, u.first_name, u.last_name, u.image as user_image
        FROM reviews r
        LEFT JOIN users u ON r.user_id = u.id
        WHERE r.watch_id = ? AND r.status = "approved"
        ORDER BY r.created_at DESC
        LIMIT 10
    ');
    $stmt->bind_param('i', $watchId);
    $stmt->execute();
    $reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Calculate average rating
    $stmt = $conn->prepare('
        SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
        FROM reviews
        WHERE watch_id = ? AND status = "approved"
    ');
    $stmt->bind_param('i', $watchId);
    $stmt->execute();
    $ratingData = $stmt->get_result()->fetch_assoc();
    
    // Get similar watches
    $stmt = $conn->prepare('
        SELECT w.*, c.name as category_name
        FROM watches w
        LEFT JOIN categories c ON w.category_id = c.id
        WHERE w.category_id = ? AND w.id != ? AND w.status = "active"
        ORDER BY w.created_at DESC
        LIMIT 4
    ');
    $stmt->bind_param('ii', $watch['category_id'], $watchId);
    $stmt->execute();
    $similarWatches = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Check if watch is in user's wishlist
    $inWishlist = false;
    if (isset($_SESSION['user'])) {
        $stmt = $conn->prepare('SELECT id FROM wishlist WHERE user_id = ? AND watch_id = ? LIMIT 1');
        $stmt->bind_param('ii', $_SESSION['user']['id'], $watchId);
        $stmt->execute();
        $inWishlist = $stmt->get_result()->num_rows > 0;
    }
    
    // Check if watch is in user's cart
    $inCart = false;
    $cartQuantity = 0;
    if (isset($_SESSION['cart'][$watchId])) {
        $inCart = true;
        $cartQuantity = $_SESSION['cart'][$watchId];
    }
    
    // Format data
    $watch['price_formatted'] = formatCurrency($watch['price']);
    $watch['original_price_formatted'] = $watch['original_price'] ? formatCurrency($watch['original_price']) : null;
    $watch['discount_percentage'] = $watch['original_price'] ? round((($watch['original_price'] - $watch['price']) / $watch['original_price']) * 100) : 0;
    
    // Format reviews
    foreach ($reviews as &$review) {
        $review['rating_stars'] = formatRatingStars($review['rating']);
        $review['created_at_formatted'] = formatDateTime($review['created_at']);
        $review['time_ago'] = getTimeAgo($review['created_at']);
        $review['user_name'] = $review['first_name'] . ' ' . $review['last_name'];
    }
    
    // Format similar watches
    foreach ($similarWatches as &$similarWatch) {
        $similarWatch['price_formatted'] = formatCurrency($similarWatch['price']);
        $similarWatch['original_price_formatted'] = $similarWatch['original_price'] ? formatCurrency($similarWatch['original_price']) : null;
    }
    
    // Prepare response
    $response = [
        'success' => true,
        'watch' => $watch,
        'images' => $images,
        'specifications' => $specifications,
        'reviews' => $reviews,
        'rating' => [
            'average' => round($ratingData['avg_rating'], 1),
            'total' => $ratingData['total_reviews'],
            'stars' => formatRatingStars($ratingData['avg_rating'])
        ],
        'similar_watches' => $similarWatches,
        'user_data' => [
            'in_wishlist' => $inWishlist,
            'in_cart' => $inCart,
            'cart_quantity' => $cartQuantity
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