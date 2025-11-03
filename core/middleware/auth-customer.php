<?php
/**
 * Customer Authentication Middleware for JackoTimespiece
 * Protects customer pages and ensures proper user access control
 */

require_once __DIR__ . '/../helpers/auth.php';

/**
 * Check if user is logged in and redirect if not
 */
function requireCustomerAuth() {
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
    
    // Check if user is not admin (for customer-only pages)
    if (isAdmin()) {
        header('Location: ../admin/index.php');
        exit;
    }
}

/**
 * Check if user is logged in (returns boolean)
 */
function checkCustomerAuth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isLoggedIn() && !isAdmin();
}

/**
 * Get customer user data
 */
function getCustomerUser() {
    if (!checkCustomerAuth()) {
        return null;
    }
    
    return getCurrentUser();
}

/**
 * Log customer activity
 */
function logCustomerActivity($action, $details = '', $conn = null) {
    if (!checkCustomerAuth()) {
        return false;
    }
    
    $userId = getCurrentUserId();
    
    if ($conn) {
        require_once __DIR__ . '/../helpers/utils.php';
        return logActivity($userId, 'customer_' . $action, $details, $conn);
    }
    
    return true;
}

/**
 * Check if user owns the resource
 */
function checkResourceOwnership($resourceUserId) {
    if (!checkCustomerAuth()) {
        return false;
    }
    
    $currentUserId = getCurrentUserId();
    return $currentUserId == $resourceUserId;
}

/**
 * Require resource ownership
 */
function requireResourceOwnership($resourceUserId) {
    if (!checkResourceOwnership($resourceUserId)) {
        header('Location: ../public/account/index.php?error=access_denied');
        exit;
    }
}

/**
 * Get customer navigation menu
 */
function getCustomerNavigation() {
    $nav = [
        'dashboard' => [
            'title' => 'Dashboard',
            'url' => '../public/account/index.php',
            'icon' => 'fas fa-tachometer-alt'
        ],
        'orders' => [
            'title' => 'My Orders',
            'url' => '../public/account/orders.php',
            'icon' => 'fas fa-shopping-bag'
        ],
        'wishlist' => [
            'title' => 'Wishlist',
            'url' => '../public/account/wishlist.php',
            'icon' => 'fas fa-heart'
        ],
        'addresses' => [
            'title' => 'Addresses',
            'url' => '../public/account/addresses.php',
            'icon' => 'fas fa-map-marker-alt'
        ],
        'settings' => [
            'title' => 'Settings',
            'url' => '../public/account/settings.php',
            'icon' => 'fas fa-cog'
        ]
    ];
    
    return $nav;
}

/**
 * Check if current page is active in customer navigation
 */
function isActiveCustomerPage($page) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    return $currentPage === $page;
}

/**
 * Get customer page title
 */
function getCustomerPageTitle() {
    $currentPage = basename($_SERVER['PHP_SELF']);
    $titles = [
        'index.php' => 'Dashboard',
        'orders.php' => 'My Orders',
        'wishlist.php' => 'Wishlist',
        'addresses.php' => 'Addresses',
        'settings.php' => 'Settings',
        'add-address.php' => 'Add Address',
        'update-password.php' => 'Change Password',
        'update-profile-pic.php' => 'Update Profile Picture'
    ];
    
    return $titles[$currentPage] ?? 'My Account';
}

/**
 * Validate customer form submission
 */
function validateCustomerForm($data, $rules) {
    require_once __DIR__ . '/../helpers/validation.php';
    
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
            
            if (strpos($rule, 'phone') !== false) {
                $phoneValidation = validatePhone($value);
                if (!$phoneValidation['valid']) {
                    $errors[$field] = $phoneValidation['error'];
                }
            }
            
            if (strpos($rule, 'password') !== false) {
                $passwordValidation = validatePassword($value);
                if (!$passwordValidation['valid']) {
                    $errors[$field] = $passwordValidation['error'];
                }
            }
            
            if (strpos($rule, 'name') !== false) {
                $nameValidation = validateName($value, ucfirst(str_replace('_', ' ', $field)));
                if (!$nameValidation['valid']) {
                    $errors[$field] = $nameValidation['error'];
                }
            }
            
            if (strpos($rule, 'address') !== false) {
                $addressValidation = validateAddress($value);
                if (!$addressValidation['valid']) {
                    $errors[$field] = $addressValidation['error'];
                }
            }
            
            if (strpos($rule, 'zip') !== false) {
                $zipValidation = validateZipCode($value);
                if (!$zipValidation['valid']) {
                    $errors[$field] = $zipValidation['error'];
                }
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
 * Display customer error message
 */
function displayCustomerError($message) {
    return '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">' . htmlspecialchars($message) . '</div>';
}

/**
 * Display customer success message
 */
function displayCustomerSuccess($message) {
    return '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">' . htmlspecialchars($message) . '</div>';
}

/**
 * Get customer statistics
 */
function getCustomerStats($userId, $conn) {
    $stats = [];
    
    // Total orders
    $stmt = $conn->prepare('SELECT COUNT(*) as count FROM orders WHERE user_id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stats['total_orders'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Total spent
    $stmt = $conn->prepare('SELECT SUM(total) as total FROM orders WHERE user_id = ? AND status = "delivered"');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stats['total_spent'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    
    // Wishlist items
    $stmt = $conn->prepare('SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stats['wishlist_items'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Pending orders
    $stmt = $conn->prepare('SELECT COUNT(*) as count FROM orders WHERE user_id = ? AND status = "pending"');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stats['pending_orders'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Recent orders
    $stmt = $conn->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stats['recent_orders'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    return $stats;
}

/**
 * Check if customer session is about to expire
 */
function checkCustomerSessionExpiry() {
    $sessionTimeout = 7200; // 2 hours for customers
    $lastActivity = $_SESSION['last_activity'] ?? 0;
    
    if (time() - $lastActivity > $sessionTimeout) {
        session_destroy();
        header('Location: ../public/login.php?error=session_expired');
        exit;
    }
    
    $_SESSION['last_activity'] = time();
}

/**
 * Update customer last activity
 */
function updateCustomerActivity() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['last_activity'] = time();
}

/**
 * Check if user can access order
 */
function canAccessOrder($orderId, $conn) {
    if (!checkCustomerAuth()) {
        return false;
    }
    
    $userId = getCurrentUserId();
    $stmt = $conn->prepare('SELECT id FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
    $stmt->bind_param('ii', $orderId, $userId);
    $stmt->execute();
    
    return $stmt->get_result()->num_rows > 0;
}

/**
 * Require order access
 */
function requireOrderAccess($orderId, $conn) {
    if (!canAccessOrder($orderId, $conn)) {
        header('Location: ../public/account/orders.php?error=access_denied');
        exit;
    }
}

/**
 * Check if user can access wishlist item
 */
function canAccessWishlistItem($wishlistId, $conn) {
    if (!checkCustomerAuth()) {
        return false;
    }
    
    $userId = getCurrentUserId();
    $stmt = $conn->prepare('SELECT id FROM wishlist WHERE id = ? AND user_id = ? LIMIT 1');
    $stmt->bind_param('ii', $wishlistId, $userId);
    $stmt->execute();
    
    return $stmt->get_result()->num_rows > 0;
}

/**
 * Require wishlist item access
 */
function requireWishlistItemAccess($wishlistId, $conn) {
    if (!canAccessWishlistItem($wishlistId, $conn)) {
        header('Location: ../public/account/wishlist.php?error=access_denied');
        exit;
    }
}

// Auto-check session expiry for customer pages
if (strpos($_SERVER['PHP_SELF'], '/account/') !== false && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    checkCustomerSessionExpiry();
}
?> 