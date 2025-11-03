<?php
/**
 * Apply Coupon API Endpoint for JackoTimespiece
 * Handles applying discount coupons to shopping cart
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../core/config/app.php';
require_once '../../core/db/connection.php';
require_once '../../core/helpers/cart.php';
require_once '../../core/helpers/validation.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    if (empty($input['coupon_code'])) {
        throw new Exception('Coupon code is required');
    }
    
    // Validate coupon code format
    $couponValidation = validateCouponCode($input['coupon_code']);
    if (!$couponValidation['valid']) {
        throw new Exception($couponValidation['error']);
    }
    
    // Sanitize input
    $couponCode = $couponValidation['code'];
    
    // Connect to database
    $conn = getConnection();
    
    // Check if cart is empty
    if (isCartEmpty()) {
        throw new Exception('Cart is empty. Add items before applying coupon.');
    }
    
    // Apply coupon
    $couponResult = applyCoupon($couponCode, $conn);
    
    if (!$couponResult['success']) {
        throw new Exception($couponResult['error']);
    }
    
    // Get updated cart summary with coupon
    $cartSummary = getCartSummary($conn);
    $finalTotal = calculateFinalTotal($conn);
    
    // Log activity if user is logged in
    if (isset($_SESSION['user'])) {
        require_once '../../core/helpers/utils.php';
        logActivity($_SESSION['user']['id'], 'apply_coupon', "Applied coupon: {$couponCode}", $conn);
    }
    
    // Prepare response
    $response = [
        'success' => true,
        'message' => 'Coupon applied successfully',
        'coupon' => [
            'code' => $couponCode,
            'discount' => $couponResult['discount'],
            'final_total' => $couponResult['final_total']
        ],
        'cart_summary' => $cartSummary,
        'final_total' => $finalTotal
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