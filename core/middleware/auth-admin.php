<?php
/**
 * Admin Authentication Middleware for JackoTimespiece
 * Protects admin pages and ensures only admin users can access them
 */

require_once __DIR__ . '/../helpers/auth.php';

/**
 * Check if user is admin and redirect if not
 */
function requireAdminAuth() {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ../public/login.php');
        exit;
    }
    
    // Check if user is admin
    if (!isAdmin()) {
        header('Location: ../public/index.php');
        exit;
    }
}

/**
 * Check if user is admin (returns boolean)
 */
function checkAdminAuth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isLoggedIn() && isAdmin();
}

/**
 * Get admin user data
 */
function getAdminUser() {
    if (!checkAdminAuth()) {
        return null;
    }
    
    return getCurrentUser();
}

/**
 * Log admin activity
 */
function logAdminActivity($action, $details = '', $conn = null) {
    if (!checkAdminAuth()) {
        return false;
    }
    
    $userId = getCurrentUserId();
    
    if ($conn) {
        require_once __DIR__ . '/../helpers/utils.php';
        return logActivity($userId, 'admin_' . $action, $details, $conn);
    }
    
    return true;
}

/**
 * Check admin permissions for specific actions
 */
function checkAdminPermission($permission) {
    if (!checkAdminAuth()) {
        return false;
    }
    
    $user = getCurrentUser();
    
    // Super admin has all permissions
    if ($user['role'] === 'admin') {
        return true;
    }
    
    // Define permission hierarchy
    $permissions = [
        'view_dashboard' => ['admin', 'manager'],
        'manage_products' => ['admin', 'manager'],
        'manage_orders' => ['admin', 'manager'],
        'manage_users' => ['admin'],
        'manage_settings' => ['admin'],
        'view_reports' => ['admin', 'manager'],
        'manage_coupons' => ['admin', 'manager']
    ];
    
    return isset($permissions[$permission]) && in_array($user['role'], $permissions[$permission]);
}

/**
 * Require specific admin permission
 */
function requireAdminPermission($permission) {
    if (!checkAdminPermission($permission)) {
        header('Location: ../admin/index.php?error=insufficient_permissions');
        exit;
    }
}

/**
 * Get admin navigation menu based on permissions
 */
function getAdminNavigation() {
    $nav = [
        'dashboard' => [
            'title' => 'Dashboard',
            'url' => '../admin/index.php',
            'icon' => 'fas fa-tachometer-alt',
            'permission' => 'view_dashboard'
        ],
        'products' => [
            'title' => 'Products',
            'url' => '../admin/products/list.php',
            'icon' => 'fas fa-box',
            'permission' => 'manage_products'
        ],
        'orders' => [
            'title' => 'Orders',
            'url' => '../admin/orders/list.php',
            'icon' => 'fas fa-shopping-cart',
            'permission' => 'manage_orders'
        ],
        'users' => [
            'title' => 'Users',
            'url' => '../admin/users/list.php',
            'icon' => 'fas fa-users',
            'permission' => 'manage_users'
        ],
        'coupons' => [
            'title' => 'Coupons',
            'url' => '../admin/coupons/list.php',
            'icon' => 'fas fa-ticket-alt',
            'permission' => 'manage_coupons'
        ],
        'settings' => [
            'title' => 'Settings',
            'url' => '../admin/settings/site.php',
            'icon' => 'fas fa-cog',
            'permission' => 'manage_settings'
        ]
    ];
    
    // Filter based on permissions
    $filteredNav = [];
    foreach ($nav as $key => $item) {
        if (checkAdminPermission($item['permission'])) {
            $filteredNav[$key] = $item;
        }
    }
    
    return $filteredNav;
}

/**
 * Check if current page is active in admin navigation
 */
function isActiveAdminPage($page) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    return $currentPage === $page;
}

/**
 * Get admin page title
 */
function getAdminPageTitle() {
    $currentPage = basename($_SERVER['PHP_SELF']);
    $titles = [
        'index.php' => 'Dashboard',
        'list.php' => 'Products',
        'add.php' => 'Add Product',
        'edit.php' => 'Edit Product',
        'orders.php' => 'Orders',
        'users.php' => 'Users',
        'coupons.php' => 'Coupons',
        'settings.php' => 'Settings'
    ];
    
    return $titles[$currentPage] ?? 'Admin Panel';
}

/**
 * Validate admin form submission
 */
function validateAdminForm($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        $value = $data[$field] ?? '';
        
        if (strpos($rule, 'required') !== false && empty($value)) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            continue;
        }
        
        if (!empty($value)) {
            if (strpos($rule, 'email') !== false) {
                $emailValidation = validateEmail($value);
                if (!$emailValidation['valid']) {
                    $errors[$field] = $emailValidation['error'];
                }
            }
            
            if (strpos($rule, 'numeric') !== false && !is_numeric($value)) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be a number';
            }
            
            if (preg_match('/min:(\d+)/', $rule, $matches)) {
                $min = $matches[1];
                if (strlen($value) < $min) {
                    $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be at least ' . $min . ' characters';
                }
            }
            
            if (preg_match('/max:(\d+)/', $rule, $matches)) {
                $max = $matches[1];
                if (strlen($value) > $max) {
                    $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be less than ' . $max . ' characters';
                }
            }
        }
    }
    
    return $errors;
}

/**
 * Display admin error message
 */
function displayAdminError($message) {
    return '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">' . htmlspecialchars($message) . '</div>';
}

/**
 * Display admin success message
 */
function displayAdminSuccess($message) {
    return '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">' . htmlspecialchars($message) . '</div>';
}

/**
 * Get admin statistics
 */
function getAdminStats($conn) {
    $stats = [];
    
    // Total products
    $result = $conn->query('SELECT COUNT(*) as count FROM watches');
    $stats['total_products'] = $result->fetch_assoc()['count'];
    
    // Total orders
    $result = $conn->query('SELECT COUNT(*) as count FROM orders');
    $stats['total_orders'] = $result->fetch_assoc()['count'];
    
    // Total users
    $result = $conn->query('SELECT COUNT(*) as count FROM users WHERE role = "customer"');
    $stats['total_customers'] = $result->fetch_assoc()['count'];
    
    // Total revenue
    $result = $conn->query('SELECT SUM(total) as total FROM orders WHERE status = "delivered"');
    $stats['total_revenue'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Pending orders
    $result = $conn->query('SELECT COUNT(*) as count FROM orders WHERE status = "pending"');
    $stats['pending_orders'] = $result->fetch_assoc()['count'];
    
    // Low stock products
    $result = $conn->query('SELECT COUNT(*) as count FROM watches WHERE stock_quantity <= 5 AND status = "active"');
    $stats['low_stock_products'] = $result->fetch_assoc()['count'];
    
    return $stats;
}

/**
 * Check if admin session is about to expire
 */
function checkAdminSessionExpiry() {
    $sessionTimeout = 3600; // 1 hour
    $lastActivity = $_SESSION['last_activity'] ?? 0;
    
    if (time() - $lastActivity > $sessionTimeout) {
        session_destroy();
        header('Location: ../public/login.php?error=session_expired');
        exit;
    }
    
    $_SESSION['last_activity'] = time();
}

// Auto-check session expiry for admin pages
if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
    checkAdminSessionExpiry();
}
?> 