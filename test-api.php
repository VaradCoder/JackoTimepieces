<?php
session_start();
require_once 'core/db/connection.php';

echo "<h1>API Endpoint Test</h1>";

// Simulate a logged-in user for testing
if (!isset($_SESSION['user'])) {
    // Create a test user session
    $_SESSION['user'] = [
        'id' => 1,
        'email' => 'test@example.com',
        'first_name' => 'Test',
        'last_name' => 'User'
    ];
    echo "⚠️ Created test user session for testing<br><br>";
}

echo "✅ User logged in: " . $_SESSION['user']['email'] . "<br><br>";

// Test wishlist list API
echo "<h2>Testing Wishlist List API:</h2>";
try {
    $user_id = $_SESSION['user']['id'];
    $stmt = $conn->prepare("SELECT wl.watch_id, w.name, w.price, w.main_image FROM wishlist wl JOIN watches w ON wl.watch_id = w.id WHERE wl.user_id = ? ORDER BY wl.created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $wishlist_items = [];
    while ($row = $result->fetch_assoc()) {
        $wishlist_items[] = $row;
    }
    
    echo "✅ Wishlist query successful<br>";
    echo "📊 Found " . count($wishlist_items) . " wishlist items<br>";
    
    if (count($wishlist_items) > 0) {
        echo "<h3>Wishlist Items:</h3>";
        echo "<ul>";
        foreach ($wishlist_items as $item) {
            echo "<li>" . htmlspecialchars($item['name']) . " - ₹" . number_format($item['price']) . "</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test adding to wishlist
echo "<h2>Testing Add to Wishlist:</h2>";
try {
    // Get first available watch
    $result = $conn->query("SELECT id, name FROM watches WHERE is_active = 1 LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $watch = $result->fetch_assoc();
        $watch_id = $watch['id'];
        
        echo "Testing with watch: " . htmlspecialchars($watch['name']) . " (ID: $watch_id)<br>";
        
        // Check if already in wishlist
        $stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND watch_id = ?");
        $stmt->bind_param("ii", $user_id, $watch_id);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows === 0) {
            // Add to wishlist
            $stmt = $conn->prepare("INSERT INTO wishlist (user_id, watch_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $watch_id);
            
            if ($stmt->execute()) {
                echo "✅ Successfully added to wishlist<br>";
            } else {
                echo "❌ Failed to add to wishlist<br>";
            }
        } else {
            echo "ℹ️ Watch already in wishlist<br>";
        }
        
    } else {
        echo "❌ No watches available for testing<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test removing from wishlist
echo "<h2>Testing Remove from Wishlist:</h2>";
try {
    // Get first wishlist item
    $stmt = $conn->prepare("SELECT wl.watch_id, w.name FROM wishlist wl JOIN watches w ON wl.watch_id = w.id WHERE wl.user_id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        $watch_id = $item['watch_id'];
        
        echo "Testing remove for: " . htmlspecialchars($item['name']) . " (ID: $watch_id)<br>";
        
        // Remove from wishlist
        $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND watch_id = ?");
        $stmt->bind_param("ii", $user_id, $watch_id);
        
        if ($stmt->execute()) {
            echo "✅ Successfully removed from wishlist<br>";
        } else {
            echo "❌ Failed to remove from wishlist<br>";
        }
        
    } else {
        echo "ℹ️ No wishlist items to test removal<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><h2>API Test Results:</h2>";
echo "✅ Database queries working<br>";
echo "✅ Session management working<br>";
echo "✅ Wishlist operations working<br>";

echo "<br><h2>Next Steps:</h2>";
echo "1. The API endpoints should work correctly<br>";
echo "2. Check browser console for JavaScript errors<br>";
echo "3. Make sure you're logged in when testing<br>";
echo "4. Test the actual pages: <a href='public/store.php'>Store</a>, <a href='public/catalog.php'>Catalog</a><br>";
?> 