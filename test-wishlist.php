<?php
session_start();
require_once 'core/db/connection.php';

echo "<h2>Database Connection Test</h2>";

// Test database connection
if ($conn) {
    echo "✅ Database connection successful<br>";
    
    // Check if wishlist table exists
    $result = $conn->query("SHOW TABLES LIKE 'wishlist'");
    if ($result->num_rows > 0) {
        echo "✅ Wishlist table exists<br>";
        
        // Check table structure
        $result = $conn->query("DESCRIBE wishlist");
        echo "<h3>Wishlist table structure:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check if there are any wishlist items
        $result = $conn->query("SELECT COUNT(*) as count FROM wishlist");
        $count = $result->fetch_assoc()['count'];
        echo "<br>📊 Total wishlist items: " . $count . "<br>";
        
    } else {
        echo "❌ Wishlist table does not exist<br>";
    }
    
    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "✅ Users table exists<br>";
        
        // Check if there are any users
        $result = $conn->query("SELECT COUNT(*) as count FROM users");
        $count = $result->fetch_assoc()['count'];
        echo "📊 Total users: " . $count . "<br>";
    } else {
        echo "❌ Users table does not exist<br>";
    }
    
    // Check if watches table exists
    $result = $conn->query("SHOW TABLES LIKE 'watches'");
    if ($result->num_rows > 0) {
        echo "✅ Watches table exists<br>";
        
        // Check if there are any watches
        $result = $conn->query("SELECT COUNT(*) as count FROM watches");
        $count = $result->fetch_assoc()['count'];
        echo "📊 Total watches: " . $count . "<br>";
    } else {
        echo "❌ Watches table does not exist<br>";
    }
    
} else {
    echo "❌ Database connection failed<br>";
}

echo "<br><h2>Session Test</h2>";
if (isset($_SESSION['user'])) {
    echo "✅ User is logged in<br>";
    echo "User ID: " . $_SESSION['user']['id'] . "<br>";
    echo "User Email: " . $_SESSION['user']['email'] . "<br>";
} else {
    echo "❌ No user logged in<br>";
}

echo "<br><h2>API Endpoints Test</h2>";
echo "<a href='api/wishlist/list.php' target='_blank'>Test Wishlist List API</a><br>";
echo "<a href='api/wishlist/add.php' target='_blank'>Test Wishlist Add API</a><br>";
echo "<a href='api/wishlist/remove.php' target='_blank'>Test Wishlist Remove API</a><br>";
echo "<a href='api/wishlist/clear.php' target='_blank'>Test Wishlist Clear API</a><br>";

echo "<br><h2>Pages Test</h2>";
echo "<a href='public/store.php' target='_blank'>Test Store Page</a><br>";
echo "<a href='public/catalog.php' target='_blank'>Test Catalog Page</a><br>";
echo "<a href='public/account/wishlist.php' target='_blank'>Test Wishlist Page</a><br>";
?> 