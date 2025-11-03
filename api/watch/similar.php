<?php
/**
 * Similar Watches API Endpoint for JackoTimespiece
 * Handles fetching similar/related watches based on various criteria
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
    // Get parameters from query string
    $watchId = $_GET['watch_id'] ?? null;
    $categoryId = $_GET['category_id'] ?? null;
    $brand = $_GET['brand'] ?? null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 8;
    $excludeIds = $_GET['exclude_ids'] ?? '';
    
    // Validate limit
    if ($limit < 1 || $limit > 20) {
        $limit = 8;
    }
    
    // Connect to database
    $conn = getConnection();
    
    $similarWatches = [];
    
    if ($watchId) {
        // Get similar watches based on specific watch
        $similarWatches = getSimilarWatchesByWatchId($watchId, $limit, $conn);
    } elseif ($categoryId) {
        // Get similar watches based on category
        $similarWatches = getSimilarWatchesByCategory($categoryId, $limit, $excludeIds, $conn);
    } elseif ($brand) {
        // Get similar watches based on brand
        $similarWatches = getSimilarWatchesByBrand($brand, $limit, $excludeIds, $conn);
    } else {
        // Get featured watches
        $similarWatches = getFeaturedWatches($limit, $conn);
    }
    
    // Format watches
    foreach ($similarWatches as &$watch) {
        $watch['price_formatted'] = formatCurrency($watch['price']);
        $watch['original_price_formatted'] = $watch['original_price'] ? formatCurrency($watch['original_price']) : null;
        $watch['discount_percentage'] = $watch['original_price'] ? round((($watch['original_price'] - $watch['price']) / $watch['original_price']) * 100) : 0;
        
        // Get average rating
        $stmt = $conn->prepare('
            SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
            FROM reviews
            WHERE watch_id = ? AND status = "approved"
        ');
        $stmt->bind_param('i', $watch['id']);
        $stmt->execute();
        $ratingData = $stmt->get_result()->fetch_assoc();
        
        $watch['rating'] = [
            'average' => round($ratingData['avg_rating'], 1),
            'total' => $ratingData['total_reviews'],
            'stars' => formatRatingStars($ratingData['avg_rating'])
        ];
    }
    
    // Prepare response
    $response = [
        'success' => true,
        'watches' => $similarWatches,
        'count' => count($similarWatches),
        'limit' => $limit
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

/**
 * Get similar watches based on specific watch ID
 */
function getSimilarWatchesByWatchId($watchId, $limit, $conn) {
    // First get the watch details
    $stmt = $conn->prepare('SELECT category_id, brand, price FROM watches WHERE id = ? AND status = "active" LIMIT 1');
    $stmt->bind_param('i', $watchId);
    $stmt->execute();
    $watch = $stmt->get_result()->fetch_assoc();
    
    if (!$watch) {
        return [];
    }
    
    // Get similar watches by category and brand
    $stmt = $conn->prepare('
        SELECT w.*, c.name as category_name
        FROM watches w
        LEFT JOIN categories c ON w.category_id = c.id
        WHERE w.id != ? AND w.status = "active"
        AND (w.category_id = ? OR w.brand = ?)
        ORDER BY 
            CASE WHEN w.category_id = ? AND w.brand = ? THEN 1
                 WHEN w.category_id = ? THEN 2
                 WHEN w.brand = ? THEN 3
                 ELSE 4
            END,
            ABS(w.price - ?) ASC,
            w.created_at DESC
        LIMIT ?
    ');
    $stmt->bind_param('iissiisii', 
        $watchId, 
        $watch['category_id'], 
        $watch['brand'],
        $watch['category_id'],
        $watch['brand'],
        $watch['category_id'],
        $watch['brand'],
        $watch['price'],
        $limit
    );
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get similar watches based on category
 */
function getSimilarWatchesByCategory($categoryId, $limit, $excludeIds, $conn) {
    $excludeArray = array_filter(array_map('intval', explode(',', $excludeIds)));
    $excludeClause = '';
    $params = [$categoryId];
    $types = 'i';
    
    if (!empty($excludeArray)) {
        $placeholders = str_repeat('?,', count($excludeArray) - 1) . '?';
        $excludeClause = "AND w.id NOT IN ($placeholders)";
        $params = array_merge($params, $excludeArray);
        $types .= str_repeat('i', count($excludeArray));
    }
    
    $params[] = $limit;
    $types .= 'i';
    
    $sql = "
        SELECT w.*, c.name as category_name
        FROM watches w
        LEFT JOIN categories c ON w.category_id = c.id
        WHERE w.category_id = ? AND w.status = 'active' $excludeClause
        ORDER BY w.featured DESC, w.created_at DESC
        LIMIT ?
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get similar watches based on brand
 */
function getSimilarWatchesByBrand($brand, $limit, $excludeIds, $conn) {
    $excludeArray = array_filter(array_map('intval', explode(',', $excludeIds)));
    $excludeClause = '';
    $params = [$brand];
    $types = 's';
    
    if (!empty($excludeArray)) {
        $placeholders = str_repeat('?,', count($excludeArray) - 1) . '?';
        $excludeClause = "AND w.id NOT IN ($placeholders)";
        $params = array_merge($params, $excludeArray);
        $types .= str_repeat('i', count($excludeArray));
    }
    
    $params[] = $limit;
    $types .= 'i';
    
    $sql = "
        SELECT w.*, c.name as category_name
        FROM watches w
        LEFT JOIN categories c ON w.category_id = c.id
        WHERE w.brand = ? AND w.status = 'active' $excludeClause
        ORDER BY w.featured DESC, w.created_at DESC
        LIMIT ?
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get featured watches
 */
function getFeaturedWatches($limit, $conn) {
    $stmt = $conn->prepare('
        SELECT w.*, c.name as category_name
        FROM watches w
        LEFT JOIN categories c ON w.category_id = c.id
        WHERE w.status = "active"
        ORDER BY w.featured DESC, w.created_at DESC
        LIMIT ?
    ');
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?> 