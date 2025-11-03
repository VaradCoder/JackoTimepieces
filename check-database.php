<?php
require_once 'core/db/connection.php';

echo "<h1>Database Setup Check</h1>";

if (!$conn) {
    echo "❌ Database connection failed";
    exit;
}

echo "✅ Database connection successful<br><br>";

// List all tables
$result = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

echo "<h2>Existing Tables:</h2>";
echo "<ul>";
foreach ($tables as $table) {
    echo "<li>✅ $table</li>";
}
echo "</ul>";

// Check required tables
$required_tables = [
    'users',
    'watches', 
    'wishlist',
    'cart_items',
    'orders',
    'order_items',
    'categories',
    'admin_users',
    'support_tickets',
    'support_messages'
];

echo "<h2>Required Tables Check:</h2>";
foreach ($required_tables as $table) {
    if (in_array($table, $tables)) {
        echo "✅ $table exists<br>";
        
        // Count records
        $count_result = $conn->query("SELECT COUNT(*) as count FROM $table");
        $count = $count_result->fetch_assoc()['count'];
        echo "&nbsp;&nbsp;&nbsp;&nbsp;📊 Records: $count<br>";
    } else {
        echo "❌ $table missing<br>";
    }
}

// Check wishlist table structure specifically
if (in_array('wishlist', $tables)) {
    echo "<h2>Wishlist Table Structure:</h2>";
    $result = $conn->query("DESCRIBE wishlist");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Test a simple wishlist query
echo "<h2>Wishlist Query Test:</h2>";
try {
    $result = $conn->query("SELECT wl.*, w.name FROM wishlist wl JOIN watches w ON wl.watch_id = w.id LIMIT 5");
    if ($result) {
        echo "✅ Wishlist query successful<br>";
        echo "📊 Found " . $result->num_rows . " wishlist items<br>";
        
        if ($result->num_rows > 0) {
            echo "<h3>Sample Wishlist Items:</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>User ID</th><th>Watch ID</th><th>Watch Name</th><th>Created</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['watch_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "❌ Wishlist query failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><h2>Next Steps:</h2>";
echo "1. If all tables exist, the wishlist should work<br>";
echo "2. Try logging in and adding items to wishlist<br>";
echo "3. Check the browser console for any JavaScript errors<br>";
echo "4. Test the API endpoints directly<br>";
?> 