<?php
/**
 * Cart Helper Functions for JackoTimespiece
 * Handles shopping cart operations and management
 */

/**
 * Initialize cart if not exists
 */
function initCart() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

/**
 * Add item to cart
 */
function addToCart($watchId, $quantity = 1) {
    initCart();
    
    if (isset($_SESSION['cart'][$watchId])) {
        $_SESSION['cart'][$watchId] += $quantity;
    } else {
        $_SESSION['cart'][$watchId] = $quantity;
    }
    
    return ['success' => true, 'message' => 'Item added to cart'];
}

/**
 * Remove item from cart
 */
function removeFromCart($watchId) {
    initCart();
    
    if (isset($_SESSION['cart'][$watchId])) {
        unset($_SESSION['cart'][$watchId]);
        return ['success' => true, 'message' => 'Item removed from cart'];
    }
    
    return ['success' => false, 'error' => 'Item not found in cart'];
}

/**
 * Update cart item quantity
 */
function updateCartQuantity($watchId, $quantity) {
    initCart();
    
    if ($quantity <= 0) {
        return removeFromCart($watchId);
    }
    
    if (isset($_SESSION['cart'][$watchId])) {
        $_SESSION['cart'][$watchId] = $quantity;
        return ['success' => true, 'message' => 'Cart updated'];
    }
    
    return ['success' => false, 'error' => 'Item not found in cart'];
}

/**
 * Clear entire cart
 */
function clearCart() {
    $_SESSION['cart'] = [];
    return ['success' => true, 'message' => 'Cart cleared'];
}

/**
 * Get cart items with product details
 */
function getCartItems($conn) {
    initCart();
    
    if (empty($_SESSION['cart'])) {
        return [];
    }
    
    $items = [];
    $total = 0;
    
    foreach ($_SESSION['cart'] as $watchId => $quantity) {
        $stmt = $conn->prepare('SELECT * FROM watches WHERE id = ? AND status = "active" LIMIT 1');
        $stmt->bind_param('i', $watchId);
        $stmt->execute();
        $watch = $stmt->get_result()->fetch_assoc();
        
        if ($watch) {
            $subtotal = $watch['price'] * $quantity;
            $total += $subtotal;
            
            $items[] = [
                'id' => $watch['id'],
                'name' => $watch['name'],
                'price' => $watch['price'],
                'image' => $watch['image'],
                'quantity' => $quantity,
                'subtotal' => $subtotal,
                'stock' => $watch['stock_quantity']
            ];
        }
    }
    
    return [
        'items' => $items,
        'total' => $total,
        'item_count' => count($items),
        'total_quantity' => array_sum($_SESSION['cart'])
    ];
}

/**
 * Get cart summary (count and total)
 */
function getCartSummary($conn) {
    $cartData = getCartItems($conn);
    return [
        'item_count' => $cartData['item_count'],
        'total_quantity' => $cartData['total_quantity'],
        'total' => $cartData['total']
    ];
}

/**
 * Check if cart is empty
 */
function isCartEmpty() {
    initCart();
    return empty($_SESSION['cart']);
}

/**
 * Get cart item count
 */
function getCartItemCount() {
    initCart();
    return array_sum($_SESSION['cart']);
}

/**
 * Validate cart items (check stock, prices, etc.)
 */
function validateCart($conn) {
    $cartData = getCartItems($conn);
    $errors = [];
    
    foreach ($cartData['items'] as $item) {
        // Check stock availability
        if ($item['quantity'] > $item['stock']) {
            $errors[] = "Insufficient stock for {$item['name']}. Available: {$item['stock']}";
        }
        
        // Check if item is still active
        if ($item['stock'] <= 0) {
            $errors[] = "{$item['name']} is out of stock";
        }
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Apply coupon to cart
 */
function applyCoupon($couponCode, $conn) {
    $stmt = $conn->prepare('SELECT * FROM coupons WHERE code = ? AND status = "active" AND (valid_until IS NULL OR valid_until > NOW()) LIMIT 1');
    $stmt->bind_param('s', $couponCode);
    $stmt->execute();
    $coupon = $stmt->get_result()->fetch_assoc();
    
    if (!$coupon) {
        return ['success' => false, 'error' => 'Invalid or expired coupon code'];
    }
    
    $cartData = getCartItems($conn);
    
    // Check minimum order amount
    if ($cartData['total'] < $coupon['min_order_amount']) {
        return ['success' => false, 'error' => 'Minimum order amount not met'];
    }
    
    // Check usage limit
    if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
        return ['success' => false, 'error' => 'Coupon usage limit reached'];
    }
    
    // Calculate discount
    if ($coupon['discount_type'] === 'percentage') {
        $discount = $cartData['total'] * ($coupon['discount_value'] / 100);
        if ($coupon['max_discount']) {
            $discount = min($discount, $coupon['max_discount']);
        }
    } else {
        $discount = $coupon['discount_value'];
    }
    
    $_SESSION['applied_coupon'] = [
        'code' => $coupon['code'],
        'discount' => $discount,
        'type' => $coupon['discount_type'],
        'value' => $coupon['discount_value']
    ];
    
    return [
        'success' => true,
        'discount' => $discount,
        'final_total' => $cartData['total'] - $discount
    ];
}

/**
 * Remove applied coupon
 */
function removeCoupon() {
    unset($_SESSION['applied_coupon']);
    return ['success' => true, 'message' => 'Coupon removed'];
}

/**
 * Get applied coupon
 */
function getAppliedCoupon() {
    return $_SESSION['applied_coupon'] ?? null;
}

/**
 * Calculate final total with tax and shipping
 */
function calculateFinalTotal($conn, $shippingMethod = 'standard') {
    $cartData = getCartItems($conn);
    $subtotal = $cartData['total'];
    
    // Apply coupon discount
    $coupon = getAppliedCoupon();
    $discount = $coupon ? $coupon['discount'] : 0;
    $discountedTotal = $subtotal - $discount;
    
    // Calculate tax (18% GST)
    $tax = $discountedTotal * 0.18;
    
    // Calculate shipping
    $shipping = 0; // Free shipping for now
    if ($shippingMethod === 'express') {
        $shipping = 500; // Express shipping cost
    }
    
    $finalTotal = $discountedTotal + $tax + $shipping;
    
    return [
        'subtotal' => $subtotal,
        'discount' => $discount,
        'discounted_total' => $discountedTotal,
        'tax' => $tax,
        'shipping' => $shipping,
        'final_total' => $finalTotal
    ];
}

/**
 * Save cart to database (for logged in users)
 */
function saveCartToDatabase($userId, $conn) {
    if (empty($_SESSION['cart'])) {
        return ['success' => true];
    }
    
    // Clear existing saved cart
    $stmt = $conn->prepare('DELETE FROM saved_carts WHERE user_id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    
    // Save current cart
    foreach ($_SESSION['cart'] as $watchId => $quantity) {
        $stmt = $conn->prepare('INSERT INTO saved_carts (user_id, watch_id, quantity, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->bind_param('iii', $userId, $watchId, $quantity);
        $stmt->execute();
    }
    
    return ['success' => true];
}

/**
 * Load cart from database (for logged in users)
 */
function loadCartFromDatabase($userId, $conn) {
    $stmt = $conn->prepare('SELECT watch_id, quantity FROM saved_carts WHERE user_id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $_SESSION['cart'] = [];
    while ($row = $result->fetch_assoc()) {
        $_SESSION['cart'][$row['watch_id']] = $row['quantity'];
    }
    
    return ['success' => true];
}

/**
 * Merge guest cart with user cart after login
 */
function mergeGuestCart($userId, $conn) {
    if (empty($_SESSION['cart'])) {
        return ['success' => true];
    }
    
    // Get user's saved cart
    $stmt = $conn->prepare('SELECT watch_id, quantity FROM saved_carts WHERE user_id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $savedCart = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Merge quantities
    foreach ($savedCart as $item) {
        if (isset($_SESSION['cart'][$item['watch_id']])) {
            $_SESSION['cart'][$item['watch_id']] += $item['quantity'];
        } else {
            $_SESSION['cart'][$item['watch_id']] = $item['quantity'];
        }
    }
    
    // Save merged cart
    return saveCartToDatabase($userId, $conn);
}
?> 