<?php
/**
 * Database Reset Script for JackoTimespiece
 * This script will drop and recreate the entire database
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'jackotimespiece';

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to MySQL successfully.\n";

// Drop database if it exists
$sql = "DROP DATABASE IF EXISTS $database";
if ($conn->query($sql) === TRUE) {
    echo "Database '$database' dropped successfully.\n";
} else {
    echo "Error dropping database: " . $conn->error . "\n";
}

// Create database
$sql = "CREATE DATABASE $database";
if ($conn->query($sql) === TRUE) {
    echo "Database '$database' created successfully.\n";
} else {
    echo "Error creating database: " . $conn->error . "\n";
}

// Select the database
$conn->select_db($database);

// Read and execute schema file
$schema_file = __DIR__ . '/database/schema.sql';
if (file_exists($schema_file)) {
    $schema = file_get_contents($schema_file);
    
    // Split by semicolon to execute multiple statements
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !str_starts_with(trim($statement), '--')) {
            if ($conn->query($statement) === TRUE) {
                echo "Schema statement executed successfully.\n";
            } else {
                echo "Error executing schema: " . $conn->error . "\n";
                echo "Statement: " . substr($statement, 0, 100) . "...\n";
            }
        }
    }
} else {
    echo "Schema file not found: $schema_file\n";
}

// Read and execute seed file
$seed_file = __DIR__ . '/database/seed.sql';
if (file_exists($seed_file)) {
    $seed = file_get_contents($seed_file);
    
    // Split by semicolon to execute multiple statements
    $statements = array_filter(array_map('trim', explode(';', $seed)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !str_starts_with(trim($statement), '--')) {
            if ($conn->query($statement) === TRUE) {
                echo "Seed statement executed successfully.\n";
            } else {
                echo "Error executing seed: " . $conn->error . "\n";
                echo "Statement: " . substr($statement, 0, 100) . "...\n";
            }
        }
    }
} else {
    echo "Seed file not found: $seed_file\n";
}

// Verify tables were created
$tables = [
    'users', 'categories', 'watches', 'orders', 'order_items', 
    'wishlist', 'coupons', 'coupon_usage', 'reviews', 'settings'
];

echo "\nVerifying tables:\n";
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "✓ Table '$table' exists\n";
    } else {
        echo "✗ Table '$table' missing\n";
    }
}

// Check sample data
echo "\nChecking sample data:\n";
$checks = [
    'categories' => 'SELECT COUNT(*) as count FROM categories',
    'watches' => 'SELECT COUNT(*) as count FROM watches',
    'users' => 'SELECT COUNT(*) as count FROM users',
    'coupons' => 'SELECT COUNT(*) as count FROM coupons',
    'settings' => 'SELECT COUNT(*) as count FROM settings'
];

foreach ($checks as $table => $query) {
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        echo "✓ $table: {$row['count']} records\n";
    } else {
        echo "✗ Error checking $table: " . $conn->error . "\n";
    }
}

echo "\nDatabase reset completed!\n";
echo "You can now use the JackoTimespiece e-commerce system.\n";
echo "\nDefault admin credentials:\n";
echo "Email: admin@jackotimespiece.com\n";
echo "Password: password\n";
echo "\nDefault customer credentials:\n";
echo "Email: customer@jackotimespiece.com\n";
echo "Password: password\n";

$conn->close();
?> 