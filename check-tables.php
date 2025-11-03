<?php
require_once 'core/db/connection.php';

echo "<h1>Database Table Check</h1>";

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

echo "<h2>All Tables:</h2>";
echo "<ul>";
foreach ($tables as $table) {
    echo "<li>📋 $table</li>";
}
echo "</ul>";

// Check specific tables we need
$important_tables = ['users', 'watches', 'wishlist', 'categories'];
echo "<h2>Important Tables Check:</h2>";
foreach ($important_tables as $table) {
    if (in_array($table, $tables)) {
        echo "✅ $table exists<br>";
        
        // Show table structure
        $result = $conn->query("DESCRIBE $table");
        echo "<h3>$table structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
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
        
        // Count records
        $count_result = $conn->query("SELECT COUNT(*) as count FROM $table");
        $count = $count_result->fetch_assoc()['count'];
        echo "📊 Records: $count<br><br>";
        
    } else {
        echo "❌ $table missing<br><br>";
    }
}

// Check if there are any products in the database
echo "<h2>Product Check:</h2>";
if (in_array('watches', $tables)) {
    $result = $conn->query("SELECT id, name, price FROM watches LIMIT 5");
    if ($result && $result->num_rows > 0) {
        echo "✅ Found watches in database:<br>";
        while ($row = $result->fetch_assoc()) {
            echo "&nbsp;&nbsp;• " . htmlspecialchars($row['name']) . " (ID: {$row['id']}, Price: ₹{$row['price']})<br>";
        }
    } else {
        echo "⚠️ No watches found in database<br>";
    }
} else {
    echo "❌ No watches table found<br>";
}

echo "<br><h2>Recommendations:</h2>";
if (!in_array('watches', $tables)) {
    echo "❌ The 'watches' table is missing. You need to run the database setup.<br>";
    echo "🔧 Run: <a href='setup-database.php'>setup-database.php</a><br>";
} else {
    echo "✅ Database structure looks good<br>";
}

if (!in_array('users', $tables)) {
    echo "❌ The 'users' table is missing. You need to run the database setup.<br>";
}

if (!in_array('wishlist', $tables)) {
    echo "❌ The 'wishlist' table is missing. You need to run the database setup.<br>";
}

echo "<br><h2>Test Links:</h2>";
echo "<a href='test-api.php'>Test API</a><br>";
echo "<a href='check-database.php'>Check Database</a><br>";
echo "<a href='setup-database.php'>Setup Database</a><br>";
?> 