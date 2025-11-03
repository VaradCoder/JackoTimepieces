<?php
session_start();
require_once __DIR__ . '/../core/db/connection.php';

// Check if order data exists
if (!isset($_SESSION['order_data']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: ../public/cart.php');
    exit;
}

// Get payment method
$payment_method = $_POST['payment_method'] ?? '';

if (empty($payment_method)) {
    header('Location: ../public/payment.php');
    exit;
}

// Get order data from session
$order_data = $_SESSION['order_data'];
$customer_info = $order_data['customer_info'];
$payment_info = $order_data['payment_info'];
$cart_items = $order_data['cart_items'];

// Generate order ID
$order_id = 'JACKO' . date('Ymd') . rand(1000, 9999);

// Get user ID if logged in
$user_id = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Insert order into database
    $order_query = "INSERT INTO orders (
        order_id, user_id, customer_name, email, phone, address, city, state, zip_code, 
        notes, shipping_method, payment_method, subtotal, tax, shipping, total, status, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
    
    $stmt = $conn->prepare($order_query);
    
    // Check if prepare statement failed
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    
    $customer_name = $customer_info['first_name'] . ' ' . $customer_info['last_name'];
    
    $bind_result = $stmt->bind_param("sissssssssssdddd", 
        $order_id,
        $user_id,
        $customer_name,
        $customer_info['email'],
        $customer_info['phone'],
        $customer_info['address'],
        $customer_info['city'],
        $customer_info['state'],
        $customer_info['zip'],
        $customer_info['notes'],
        $customer_info['shipping_method'],
        $payment_method,
        $payment_info['subtotal'],
        $payment_info['tax'],
        $payment_info['shipping'],
        $payment_info['total']
    );
    
    // Check if bind_param failed
    if (!$bind_result) {
        throw new Exception("Bind parameter failed: " . $stmt->error);
    }
    
    $execute_result = $stmt->execute();
    
    // Check if execute failed
    if (!$execute_result) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $order_db_id = $conn->insert_id;
    
    // Insert order items
    foreach ($cart_items as $item) {
        list($watch_id, $quantity) = explode(':', $item);
        
        // Get watch details
        $watch_query = "SELECT name, price FROM watches WHERE id = ?";
        $watch_stmt = $conn->prepare($watch_query);
        
        if (!$watch_stmt) {
            throw new Exception("Watch prepare statement failed: " . $conn->error);
        }
        
        $watch_stmt->bind_param("i", $watch_id);
        $watch_stmt->execute();
        $watch_result = $watch_stmt->get_result();
        $watch = $watch_result->fetch_assoc();
        
        // Insert order item
        $item_query = "INSERT INTO order_items (order_id, watch_id, watch_name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)";
        $item_stmt = $conn->prepare($item_query);
        
        if (!$item_stmt) {
            throw new Exception("Order item prepare statement failed: " . $conn->error);
        }
        
        $subtotal = $watch['price'] * $quantity;
        
        $item_stmt->bind_param("iisdis", 
            $order_db_id,
            $watch_id,
            $watch['name'],
            $watch['price'],
            $quantity,
            $subtotal
        );
        
        $item_stmt->execute();
    }
    
    // Update order status based on payment method
    $status = ($payment_method === 'cash') ? 'pending' : 'paid';
    $status_query = "UPDATE orders SET status = ? WHERE id = ?";
    $status_stmt = $conn->prepare($status_query);
    
    if (!$status_stmt) {
        throw new Exception("Status update prepare statement failed: " . $conn->error);
    }
    
    $status_stmt->bind_param("si", $status, $order_db_id);
    $status_stmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    // Clear cart
    unset($_SESSION['cart']);
    
    // Store order info for confirmation page
    $_SESSION['order_confirmation'] = [
        'order_id' => $order_id,
        'order_db_id' => $order_db_id,
        'customer_name' => $customer_name,
        'total' => $payment_info['total'],
        'payment_method' => $payment_method,
        'status' => $status
    ];
    
    // Redirect to confirmation page
    header('Location: ../public/confirmation.php');
    exit;
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->connect_errno === 0) {
        $conn->rollback();
    }
    
    // Log error (in production, you'd want proper error logging)
    error_log("Order processing error: " . $e->getMessage());
    
    // Redirect back to payment page with error
    $_SESSION['payment_error'] = "There was an error processing your order: " . $e->getMessage();
    header('Location: ../public/payment.php');
    exit;
}
?> 