<?php
/**
 * Product Database Operations for JackoTimespiece
 * Handles all product-related database operations
 */

require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/../helpers/utils.php';

/**
 * Get all products with optional filtering
 */
function getAllProducts($conn, $filters = [], $sort = 'newest', $limit = 50, $offset = 0) {
    $whereConditions = ['w.status = "active"'];
    $params = [];
    $types = '';

    // Apply filters
    if (!empty($filters['category_id'])) {
        $whereConditions[] = 'w.category_id = ?';
        $params[] = $filters['category_id'];
        $types .= 'i';
    }

    if (!empty($filters['brand'])) {
        $whereConditions[] = 'w.brand = ?';
        $params[] = $filters['brand'];
        $types .= 's';
    }

    if (!empty($filters['gender'])) {
        $whereConditions[] = 'w.gender = ?';
        $params[] = $filters['gender'];
        $types .= 's';
    }

    if (!empty($filters['price_min'])) {
        $whereConditions[] = 'w.price >= ?';
        $params[] = $filters['price_min'];
        $types .= 'd';
    }

    if (!empty($filters['price_max'])) {
        $whereConditions[] = 'w.price <= ?';
        $params[] = $filters['price_max'];
        $types .= 'd';
    }

    if (!empty($filters['search'])) {
        $whereConditions[] = '(w.name LIKE ? OR w.brand LIKE ? OR w.model LIKE ? OR w.description LIKE ?)';
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'ssss';
    }

    if (!empty($filters['featured'])) {
        $whereConditions[] = 'w.featured = 1';
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
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get product by ID
 */
function getProductById($conn, $productId) {
    $stmt = $conn->prepare('
        SELECT w.*, c.name as category_name, c.slug as category_slug
        FROM watches w
        LEFT JOIN categories c ON w.category_id = c.id
        WHERE w.id = ? AND w.status = "active"
        LIMIT 1
    ');
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Get featured products
 */
function getFeaturedProducts($conn, $limit = 8) {
    $stmt = $conn->prepare('
        SELECT w.*, c.name as category_name
        FROM watches w
        LEFT JOIN categories c ON w.category_id = c.id
        WHERE w.status = "active" AND w.featured = 1
        ORDER BY w.created_at DESC
        LIMIT ?
    ');
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get recent products
 */
function getRecentProducts($conn, $limit = 8) {
    $stmt = $conn->prepare('
        SELECT w.*, c.name as category_name
        FROM watches w
        LEFT JOIN categories c ON w.category_id = c.id
        WHERE w.status = "active"
        ORDER BY w.created_at DESC
        LIMIT ?
    ');
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get similar products
 */
function getSimilarProducts($conn, $productId, $limit = 4) {
    // First get the current product details
    $product = getProductById($conn, $productId);
    if (!$product) {
        return [];
    }

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
    $stmt->bind_param('iissiisdi', 
        $productId, 
        $product['category_id'], 
        $product['brand'],
        $product['category_id'],
        $product['brand'],
        $product['category_id'],
        $product['brand'],
        $product['price'],
        $limit
    );
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get products by category
 */
function getProductsByCategory($conn, $categoryId, $limit = 12, $offset = 0) {
    $stmt = $conn->prepare('
        SELECT w.*, c.name as category_name
        FROM watches w
        LEFT JOIN categories c ON w.category_id = c.id
        WHERE w.category_id = ? AND w.status = "active"
        ORDER BY w.created_at DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->bind_param('iii', $categoryId, $limit, $offset);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get products by brand
 */
function getProductsByBrand($conn, $brand, $limit = 12, $offset = 0) {
    $stmt = $conn->prepare('
        SELECT w.*, c.name as category_name
        FROM watches w
        LEFT JOIN categories c ON w.category_id = c.id
        WHERE w.brand = ? AND w.status = "active"
        ORDER BY w.created_at DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->bind_param('sii', $brand, $limit, $offset);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Search products
 */
function searchProducts($conn, $searchTerm, $limit = 20) {
    $stmt = $conn->prepare('
        SELECT w.*, c.name as category_name
        FROM watches w
        LEFT JOIN categories c ON w.category_id = c.id
        WHERE w.status = "active"
        AND (w.name LIKE ? OR w.brand LIKE ? OR w.model LIKE ? OR w.description LIKE ?)
        ORDER BY 
            CASE WHEN w.name LIKE ? THEN 1
                 WHEN w.brand LIKE ? THEN 2
                 WHEN w.model LIKE ? THEN 3
                 ELSE 4
            END,
            w.created_at DESC
        LIMIT ?
    ');
    
    $searchPattern = '%' . $searchTerm . '%';
    $exactPattern = $searchTerm . '%';
    $stmt->bind_param('sssssssi', 
        $searchPattern, $searchPattern, $searchPattern, $searchPattern,
        $exactPattern, $exactPattern, $exactPattern, $limit
    );
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get product count with filters
 */
function getProductCount($conn, $filters = []) {
    $whereConditions = ['w.status = "active"'];
    $params = [];
    $types = '';

    // Apply filters
    if (!empty($filters['category_id'])) {
        $whereConditions[] = 'w.category_id = ?';
        $params[] = $filters['category_id'];
        $types .= 'i';
    }

    if (!empty($filters['brand'])) {
        $whereConditions[] = 'w.brand = ?';
        $params[] = $filters['brand'];
        $types .= 's';
    }

    if (!empty($filters['gender'])) {
        $whereConditions[] = 'w.gender = ?';
        $params[] = $filters['gender'];
        $types .= 's';
    }

    if (!empty($filters['price_min'])) {
        $whereConditions[] = 'w.price >= ?';
        $params[] = $filters['price_min'];
        $types .= 'd';
    }

    if (!empty($filters['price_max'])) {
        $whereConditions[] = 'w.price <= ?';
        $params[] = $filters['price_max'];
        $types .= 'd';
    }

    if (!empty($filters['search'])) {
        $whereConditions[] = '(w.name LIKE ? OR w.brand LIKE ? OR w.model LIKE ? OR w.description LIKE ?)';
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'ssss';
    }

    $whereClause = implode(' AND ', $whereConditions);

    $sql = "SELECT COUNT(*) as total FROM watches w WHERE $whereClause";
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    return $result['total'];
}

/**
 * Create new product
 */
function createProduct($conn, $productData) {
    $stmt = $conn->prepare('
        INSERT INTO watches (
            name, brand, model, description, price, original_price, 
            category_id, gender, stock_quantity, image, featured, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ');
    
    $stmt->bind_param('ssssddissis', 
        $productData['name'],
        $productData['brand'],
        $productData['model'],
        $productData['description'],
        $productData['price'],
        $productData['original_price'],
        $productData['category_id'],
        $productData['gender'],
        $productData['stock_quantity'],
        $productData['image'],
        $productData['featured'],
        $productData['status']
    );
    
    if ($stmt->execute()) {
        return $stmt->insert_id;
    }
    
    return false;
}

/**
 * Update product
 */
function updateProduct($conn, $productId, $productData) {
    $stmt = $conn->prepare('
        UPDATE watches SET 
            name = ?, brand = ?, model = ?, description = ?, 
            price = ?, original_price = ?, category_id = ?, gender = ?, 
            stock_quantity = ?, image = ?, featured = ?, status = ?, updated_at = NOW()
        WHERE id = ?
    ');
    
    $stmt->bind_param('ssssddissisi', 
        $productData['name'],
        $productData['brand'],
        $productData['model'],
        $productData['description'],
        $productData['price'],
        $productData['original_price'],
        $productData['category_id'],
        $productData['gender'],
        $productData['stock_quantity'],
        $productData['image'],
        $productData['featured'],
        $productData['status'],
        $productId
    );
    
    return $stmt->execute();
}

/**
 * Delete product
 */
function deleteProduct($conn, $productId) {
    // First check if product exists
    $product = getProductById($conn, $productId);
    if (!$product) {
        return false;
    }

    // Delete related records first
    $conn->begin_transaction();
    
    try {
        // Delete reviews
        $stmt = $conn->prepare('DELETE FROM reviews WHERE watch_id = ?');
        $stmt->bind_param('i', $productId);
        $stmt->execute();

        // Delete wishlist items
        $stmt = $conn->prepare('DELETE FROM wishlist WHERE watch_id = ?');
        $stmt->bind_param('i', $productId);
        $stmt->execute();

        // Delete cart items (from saved carts)
        $stmt = $conn->prepare('DELETE FROM saved_carts WHERE watch_id = ?');
        $stmt->bind_param('i', $productId);
        $stmt->execute();

        // Delete order items
        $stmt = $conn->prepare('DELETE FROM order_items WHERE watch_id = ?');
        $stmt->bind_param('i', $productId);
        $stmt->execute();

        // Delete product images
        $stmt = $conn->prepare('DELETE FROM watch_images WHERE watch_id = ?');
        $stmt->bind_param('i', $productId);
        $stmt->execute();

        // Delete product specifications
        $stmt = $conn->prepare('DELETE FROM watch_specifications WHERE watch_id = ?');
        $stmt->bind_param('i', $productId);
        $stmt->execute();

        // Finally delete the product
        $stmt = $conn->prepare('DELETE FROM watches WHERE id = ?');
        $stmt->bind_param('i', $productId);
        $stmt->execute();

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

/**
 * Update product stock
 */
function updateProductStock($conn, $productId, $quantity) {
    $stmt = $conn->prepare('UPDATE watches SET stock_quantity = ?, updated_at = NOW() WHERE id = ?');
    $stmt->bind_param('ii', $quantity, $productId);
    return $stmt->execute();
}

/**
 * Decrease product stock (for orders)
 */
function decreaseProductStock($conn, $productId, $quantity) {
    $stmt = $conn->prepare('UPDATE watches SET stock_quantity = stock_quantity - ?, updated_at = NOW() WHERE id = ? AND stock_quantity >= ?');
    $stmt->bind_param('iii', $quantity, $productId, $quantity);
    return $stmt->execute() && $stmt->affected_rows > 0;
}

/**
 * Get low stock products
 */
function getLowStockProducts($conn, $threshold = 5, $limit = 10) {
    $stmt = $conn->prepare('
        SELECT w.*, c.name as category_name
        FROM watches w
        LEFT JOIN categories c ON w.category_id = c.id
        WHERE w.stock_quantity <= ? AND w.status = "active"
        ORDER BY w.stock_quantity ASC
        LIMIT ?
    ');
    $stmt->bind_param('ii', $threshold, $limit);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get product statistics
 */
function getProductStats($conn) {
    $stats = [];
    
    // Total products
    $result = $conn->query('SELECT COUNT(*) as count FROM watches');
    $stats['total_products'] = $result->fetch_assoc()['count'];
    
    // Active products
    $result = $conn->query('SELECT COUNT(*) as count FROM watches WHERE status = "active"');
    $stats['active_products'] = $result->fetch_assoc()['count'];
    
    // Featured products
    $result = $conn->query('SELECT COUNT(*) as count FROM watches WHERE featured = 1 AND status = "active"');
    $stats['featured_products'] = $result->fetch_assoc()['count'];
    
    // Low stock products
    $result = $conn->query('SELECT COUNT(*) as count FROM watches WHERE stock_quantity <= 5 AND status = "active"');
    $stats['low_stock_products'] = $result->fetch_assoc()['count'];
    
    // Out of stock products
    $result = $conn->query('SELECT COUNT(*) as count FROM watches WHERE stock_quantity = 0 AND status = "active"');
    $stats['out_of_stock_products'] = $result->fetch_assoc()['count'];
    
    // Total value of inventory
    $result = $conn->query('SELECT SUM(price * stock_quantity) as total_value FROM watches WHERE status = "active"');
    $stats['inventory_value'] = $result->fetch_assoc()['total_value'] ?? 0;
    
    return $stats;
}

/**
 * Get product categories
 */
function getProductCategories($conn) {
    $stmt = $conn->prepare('SELECT * FROM categories ORDER BY name');
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get product brands
 */
function getProductBrands($conn) {
    $stmt = $conn->prepare('SELECT DISTINCT brand FROM watches WHERE brand IS NOT NULL ORDER BY brand');
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Validate product data
 */
function validateProductData($data) {
    $errors = [];
    
    if (empty($data['name'])) {
        $errors['name'] = 'Product name is required';
    }
    
    if (empty($data['brand'])) {
        $errors['brand'] = 'Brand is required';
    }
    
    if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
        $errors['price'] = 'Valid price is required';
    }
    
    if (empty($data['stock_quantity']) || !is_numeric($data['stock_quantity']) || $data['stock_quantity'] < 0) {
        $errors['stock_quantity'] = 'Valid stock quantity is required';
    }
    
    if (!empty($data['original_price']) && $data['original_price'] <= $data['price']) {
        $errors['original_price'] = 'Original price must be greater than current price';
    }
    
    return $errors;
}
?> 