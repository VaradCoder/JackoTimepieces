<?php
/**
 * Database Setup Script for JackoTimespiece
 * This script creates the database and all tables with sample data
 */

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'jackotimespiece';

echo "Setting up JackoTimespiece database...\n\n";

try {
    // Create connection without database
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if it doesn't exist
    echo "Creating database...\n";
    $sql = "CREATE DATABASE IF NOT EXISTS $db_name";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully or already exists.\n";
    } else {
        echo "Error creating database: " . $conn->error . "\n";
    }
    
    // Select the database
    $conn->select_db($db_name);
    
    // Read and execute schema
    echo "Creating tables...\n";
    $schema = file_get_contents('database/schema.sql');
    $queries = explode(';', $schema);
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            if ($conn->query($query) === TRUE) {
                echo "✓ Table created/updated successfully.\n";
            } else {
                echo "✗ Error creating table: " . $conn->error . "\n";
            }
        }
    }
    
    // Read and execute seed data
    echo "\nInserting sample data...\n";
    $seed = file_get_contents('database/seed.sql');
    $queries = explode(';', $seed);
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            if ($conn->query($query) === TRUE) {
                echo "✓ Sample data inserted successfully.\n";
            } else {
                echo "✗ Error inserting data: " . $conn->error . "\n";
            }
        }
    }
    
    // Create default admin user
    echo "\nCreating default admin user...\n";
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $admin_sql = "INSERT INTO admin_users (first_name, last_name, email, password, role) 
                   VALUES ('Super', 'Admin', 'admin@jackotimespiece.com', '$admin_password', 'super_admin')
                   ON DUPLICATE KEY UPDATE id=id";
    
    if ($conn->query($admin_sql) === TRUE) {
        echo "✓ Default admin user created.\n";
        echo "Email: admin@jackotimespiece.com\n";
        echo "Password: admin123\n";
    } else {
        echo "✗ Error creating admin user: " . $conn->error . "\n";
    }
    
    // Create default customer user
    echo "\nCreating default customer user...\n";
    $customer_password = password_hash('customer123', PASSWORD_DEFAULT);
    $customer_sql = "INSERT INTO users (first_name, last_name, email, password, is_admin) 
                     VALUES ('John', 'Customer', 'customer@jackotimespiece.com', '$customer_password', 0)
                     ON DUPLICATE KEY UPDATE id=id";
    
    if ($conn->query($customer_sql) === TRUE) {
        echo "✓ Default customer user created.\n";
        echo "Email: customer@jackotimespiece.com\n";
        echo "Password: customer123\n";
    } else {
        echo "✗ Error creating customer user: " . $conn->error . "\n";
    }
    
    // Insert additional sample data
    echo "\nInserting additional sample data...\n";
    
    // Sample orders
    $order_sql = "INSERT INTO orders (order_id, user_id, status, total, subtotal, payment_status, created_at) VALUES
                   ('ORD001', 2, 'delivered', 8500.00, 8500.00, 'paid', DATE_SUB(NOW(), INTERVAL 5 DAY)),
                   ('ORD002', 2, 'shipped', 12400.00, 12400.00, 'paid', DATE_SUB(NOW(), INTERVAL 2 DAY)),
                   ('ORD003', 2, 'processing', 4800.00, 4800.00, 'paid', NOW())";
    
    if ($conn->query($order_sql) === TRUE) {
        echo "✓ Sample orders created.\n";
    } else {
        echo "✗ Error creating orders: " . $conn->error . "\n";
    }
    
    // Sample order items
    $order_items_sql = "INSERT INTO order_items (order_id, watch_id, quantity, price, total) VALUES
                        (1, 1, 1, 8500.00, 8500.00),
                        (2, 2, 2, 6200.00, 12400.00),
                        (3, 3, 1, 4800.00, 4800.00)";
    
    if ($conn->query($order_items_sql) === TRUE) {
        echo "✓ Sample order items created.\n";
    } else {
        echo "✗ Error creating order items: " . $conn->error . "\n";
    }
    
    // Sample wishlist items
    $wishlist_sql = "INSERT INTO wishlist (user_id, watch_id) VALUES
                     (2, 4),
                     (2, 5),
                     (2, 6)";
    
    if ($conn->query($wishlist_sql) === TRUE) {
        echo "✓ Sample wishlist items created.\n";
    } else {
        echo "✗ Error creating wishlist items: " . $conn->error . "\n";
    }
    
    // Sample support tickets
    $tickets_sql = "INSERT INTO support_tickets (user_id, subject, message, status, priority) VALUES
                    (2, 'Order Status Inquiry', 'I would like to know the status of my recent order ORD002.', 'open', 'medium'),
                    (2, 'Product Question', 'Do you have this watch in different colors?', 'in_progress', 'low')";
    
    if ($conn->query($tickets_sql) === TRUE) {
        echo "✓ Sample support tickets created.\n";
    } else {
        echo "✗ Error creating support tickets: " . $conn->error . "\n";
    }
    
    // Sample support messages
    $messages_sql = "INSERT INTO support_messages (ticket_id, user_id, message, is_admin_reply) VALUES
                     (1, 2, 'I would like to know the status of my recent order ORD002.', 0),
                     (2, 2, 'Do you have this watch in different colors?', 0)";
    
    if ($conn->query($messages_sql) === TRUE) {
        echo "✓ Sample support messages created.\n";
    } else {
        echo "✗ Error creating support messages: " . $conn->error . "\n";
    }
    
    echo "\n🎉 Database setup completed successfully!\n\n";
    echo "Default Admin Login:\n";
    echo "Email: admin@jackotimespiece.com\n";
    echo "Password: admin123\n\n";
    echo "Default Customer Login:\n";
    echo "Email: customer@jackotimespiece.com\n";
    echo "Password: customer123\n\n";
    echo "Admin Registration Codes:\n";
    echo "- JACKO2024\n";
    echo "- ADMIN2024\n";
    echo "- SUPER2024\n\n";
    echo "You can now access the website at: http://localhost/Watch/public/\n";
    echo "Admin panel at: http://localhost/Watch/admin/\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 