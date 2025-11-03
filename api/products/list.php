<?php
/**
 * Products List API Endpoint for JackoTimespiece
 * Handles fetching and filtering products with pagination and search
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
    // Get query parameters
    $category = $_GET['category'] ?? '';
    $brand = $_GET['brand'] ?? '';
    $price_min = $_GET['price_min'] ?? '';
    $price_max = $_GET['price_max'] ?? '';
    $sort = $_GET['sort'] ?? 'newest';
    $search = $_GET['search'] ?? '';
    $gender = $_GET['gender'] ?? '';
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = min(50, max(1, intval($_GET['limit'] ?? 12)));
    $offset = ($page - 1) * $limit;

    // Connect to database
    $conn = getConnection();

    // Build WHERE clause
    $whereConditions = ['w.status = "active"'];
    $params = [];
    $types = '';

    if (!empty($category)) {
        $whereConditions[] = 'w.category_id = ?';
        $params[] = $category;
        $types .= 'i';
    }

    if (!empty($brand)) {
        $whereConditions[] = 'w.brand = ?';
        $params[] = $brand;
        $types .= 's';
    }

    if (!empty($price_min)) {
        $whereConditions[] = 'w.price >= ?';
        $params[] = $price_min;
        $types .= 'd';
    }

    if (!empty($price_max)) {
        $whereConditions[] = 'w.price <= ?';
        $params[] = $price_max;
        $types .= 'd';
    }

    if (!empty($search)) {
        $whereConditions[] = '(w.name LIKE ? OR w.brand LIKE ? OR w.model LIKE ? OR w.description LIKE ?)';
        $searchTerm = '%' . $search . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'ssss';
    }

    if (!empty($gender)) {
        $whereConditions[] = 'w.gender = ?';
        $params[] = $gender;
        $types .= 's';
    }

    $whereClause = implode(' AND ', $whereConditions);

    // Build ORDER BY clause
    $orderBy = match($sort) {
        'price_low' => 'w.price ASC',
        'price_high' => 'w.price DESC',
        'name' => 'w.name ASC',
        'brand' => 'w.brand ASC',
        'oldest' => 'w.created_at ASC',
        'featured' => 'w.featured DESC, w.created_at DESC',
        default => 'w.created_at DESC'
    };

    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM watches w WHERE $whereClause";
    $stmt = $conn->prepare($countSql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $totalResult = $stmt->get_result()->fetch_assoc();
    $total = $totalResult['total'];

    // Get products
    $sql = "
        SELECT w.*, c.name as category_name, c.slug as category_slug,
               (SELECT AVG(rating) FROM reviews WHERE watch_id = w.id AND status = 'approved') as avg_rating,
               (SELECT COUNT(*) FROM reviews WHERE watch_id = w.id AND status = 'approved') as review_count
        FROM watches w
        LEFT JOIN categories c ON w.category_id = c.id
        WHERE $whereClause
        ORDER BY $orderBy
        LIMIT ? OFFSET ?
    ";

    $stmt = $conn->prepare($sql);
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Format products
    foreach ($products as &$product) {
        $product['price_formatted'] = formatCurrency($product['price']);
        $product['original_price_formatted'] = $product['original_price'] ? formatCurrency($product['original_price']) : null;
        $product['discount_percentage'] = $product['original_price'] ? 
            round((($product['original_price'] - $product['price']) / $product['original_price']) * 100) : 0;
        
        $product['rating'] = [
            'average' => round($product['avg_rating'], 1),
            'total' => $product['review_count'],
            'stars' => formatRatingStars($product['avg_rating'])
        ];

        // Remove raw rating data
        unset($product['avg_rating'], $product['review_count']);
    }

    // Get available filters
    $filters = getAvailableFilters($conn, $whereConditions, $params, $types);

    // Calculate pagination
    $totalPages = ceil($total / $limit);
    $pagination = [
        'current_page' => $page,
        'total_pages' => $totalPages,
        'per_page' => $limit,
        'total' => $total,
        'has_previous' => $page > 1,
        'has_next' => $page < $totalPages,
        'previous_page' => $page - 1,
        'next_page' => $page + 1
    ];

    // Prepare response
    $response = [
        'success' => true,
        'products' => $products,
        'pagination' => $pagination,
        'filters' => $filters,
        'applied_filters' => [
            'category' => $category,
            'brand' => $brand,
            'price_min' => $price_min,
            'price_max' => $price_max,
            'sort' => $sort,
            'search' => $search,
            'gender' => $gender
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

/**
 * Get available filters based on current results
 */
function getAvailableFilters($conn, $whereConditions, $params, $types) {
    $filters = [];

    // Get categories
    $categorySql = "
        SELECT DISTINCT c.id, c.name, c.slug, COUNT(w.id) as count
        FROM categories c
        LEFT JOIN watches w ON c.id = w.category_id AND w.status = 'active'
        GROUP BY c.id, c.name, c.slug
        HAVING count > 0
        ORDER BY c.name
    ";
    $stmt = $conn->prepare($categorySql);
    $stmt->execute();
    $filters['categories'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get brands
    $brandSql = "
        SELECT DISTINCT brand, COUNT(*) as count
        FROM watches
        WHERE status = 'active'
        GROUP BY brand
        HAVING count > 0
        ORDER BY brand
    ";
    $stmt = $conn->prepare($brandSql);
    $stmt->execute();
    $filters['brands'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get price range
    $priceSql = "
        SELECT MIN(price) as min_price, MAX(price) as max_price
        FROM watches
        WHERE status = 'active'
    ";
    $stmt = $conn->prepare($priceSql);
    $stmt->execute();
    $filters['price_range'] = $stmt->get_result()->fetch_assoc();

    // Get genders
    $genderSql = "
        SELECT DISTINCT gender, COUNT(*) as count
        FROM watches
        WHERE status = 'active'
        GROUP BY gender
        HAVING count > 0
        ORDER BY gender
    ";
    $stmt = $conn->prepare($genderSql);
    $stmt->execute();
    $filters['genders'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    return $filters;
}
?> 