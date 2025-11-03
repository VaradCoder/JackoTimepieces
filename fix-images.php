<?php
/**
 * Fix Images Script for JackoTimespiece
 * Updates database to use existing images
 */

require_once 'core/db/connection.php';

echo "Fixing image references in database...\n";

// Get existing images in the watches directory
$watches_dir = 'assets/images/watches/';
$existing_images = [];
if (is_dir($watches_dir)) {
    $files = scandir($watches_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'jpg') {
            $existing_images[] = $file;
        }
    }
}

echo "Found existing images: " . implode(', ', $existing_images) . "\n";

if (empty($existing_images)) {
    echo "No images found in watches directory. Creating default images...\n";
    
    // Create some default images by copying existing ones with new names
    $default_images = [
        'rolex-submariner.jpg' => 'Black.jpg',
        'omega-speedmaster.jpg' => 'Golden.jpg',
        'cartier-tank.jpg' => 'Black.jpg',
        'patek-calatrava.jpg' => 'Golden.jpg',
        'ap-royal-oak.jpg' => 'Black.jpg',
        'tag-monaco.jpg' => 'Golden.jpg',
        'iwc-portugieser.jpg' => 'Black.jpg',
        'breitling-navitimer.jpg' => 'Golden.jpg',
        'jlc-reverso.jpg' => 'Black.jpg',
        'hublot-big-bang.jpg' => 'Golden.jpg'
    ];
    
    foreach ($default_images as $new_name => $source_name) {
        $source_path = $watches_dir . $source_name;
        $dest_path = $watches_dir . $new_name;
        
        if (file_exists($source_path) && !file_exists($dest_path)) {
            if (copy($source_path, $dest_path)) {
                echo "Created image: $new_name\n";
                $existing_images[] = $new_name;
            } else {
                echo "Failed to create image: $new_name\n";
            }
        }
    }
}

// Check if watches table exists and has data
$check_query = "SHOW TABLES LIKE 'watches'";
$result = $conn->query($check_query);

if ($result && $result->num_rows > 0) {
    echo "Watches table exists.\n";
    
    // Count watches
    $count_query = "SELECT COUNT(*) as count FROM watches";
    $count_result = $conn->query($count_query);
    $count = $count_result->fetch_assoc()['count'];
    echo "Found $count watches in database.\n";
    
    if ($count > 0) {
        // Update watches table to use existing images
        $watches_query = "SELECT id, name, image FROM watches";
        $result = $conn->query($watches_query);

        if ($result && $result->num_rows > 0) {
            $image_index = 0;
            $total_images = count($existing_images);
            
            while ($watch = $result->fetch_assoc()) {
                // Cycle through existing images
                $new_image = $existing_images[$image_index % $total_images];
                
                $update_query = "UPDATE watches SET image = ? WHERE id = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param('si', $new_image, $watch['id']);
                
                if ($stmt->execute()) {
                    echo "Updated watch '{$watch['name']}' to use image: $new_image\n";
                } else {
                    echo "Error updating watch '{$watch['name']}': " . $conn->error . "\n";
                }
                
                $image_index++;
            }
        }
    } else {
        echo "No watches found in database. You may need to import seed data first.\n";
    }
} else {
    echo "Watches table does not exist. You may need to import schema first.\n";
}

echo "Image fix completed!\n";
?> 