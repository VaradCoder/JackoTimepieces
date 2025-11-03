<?php
/**
 * Utility Helper Functions for JackoTimespiece
 * Common utility functions used throughout the application
 */

/**
 * Format currency (Indian Rupees)
 */
function formatCurrency($amount) {
    return '₹' . number_format($amount, 0, '.', ',');
}

/**
 * Format date
 */
function formatDate($date, $format = 'd M Y') {
    return date($format, strtotime($date));
}

/**
 * Format datetime
 */
function formatDateTime($datetime, $format = 'd M Y, h:i A') {
    return date($format, strtotime($datetime));
}

/**
 * Generate random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $string;
}

/**
 * Generate order ID
 */
function generateOrderId() {
    return 'JACKO' . date('Ymd') . rand(1000, 9999);
}

/**
 * Sanitize input
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone number (Indian format)
 */
function validatePhone($phone) {
    return preg_match('/^[6-9]\d{9}$/', $phone);
}

/**
 * Validate password strength
 */
function validatePassword($password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
    return strlen($password) >= 8 && 
           preg_match('/[A-Z]/', $password) && 
           preg_match('/[a-z]/', $password) && 
           preg_match('/[0-9]/', $password);
}

/**
 * Upload file
 */
function uploadFile($file, $destination, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'], $maxSize = 5242880) {
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File too large. Maximum size is ' . ($maxSize / 1024 / 1024) . 'MB'];
    }
    
    // Check file type
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes)];
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $fileExtension;
    $filepath = $destination . '/' . $filename;
    
    // Create directory if it doesn't exist
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
    }
    
    return ['success' => false, 'error' => 'Failed to upload file'];
}

/**
 * Delete file
 */
function deleteFile($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Get file extension
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Resize image
 */
function resizeImage($sourcePath, $destinationPath, $width, $height, $quality = 80) {
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        return false;
    }
    
    $sourceWidth = $imageInfo[0];
    $sourceHeight = $imageInfo[1];
    $sourceType = $imageInfo[2];
    
    // Create source image
    switch ($sourceType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }
    
    // Create destination image
    $destinationImage = imagecreatetruecolor($width, $height);
    
    // Preserve transparency for PNG
    if ($sourceType === IMAGETYPE_PNG) {
        imagealphablending($destinationImage, false);
        imagesavealpha($destinationImage, true);
    }
    
    // Resize
    imagecopyresampled($destinationImage, $sourceImage, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);
    
    // Save
    switch ($sourceType) {
        case IMAGETYPE_JPEG:
            imagejpeg($destinationImage, $destinationPath, $quality);
            break;
        case IMAGETYPE_PNG:
            imagepng($destinationImage, $destinationPath, 9);
            break;
        case IMAGETYPE_GIF:
            imagegif($destinationImage, $destinationPath);
            break;
    }
    
    // Clean up
    imagedestroy($sourceImage);
    imagedestroy($destinationImage);
    
    return true;
}

/**
 * Create thumbnail
 */
function createThumbnail($sourcePath, $destinationPath, $size = 150) {
    return resizeImage($sourcePath, $destinationPath, $size, $size);
}

/**
 * Send email
 */
function sendEmail($to, $subject, $message, $from = 'noreply@jackotimespiece.com') {
    $headers = "From: $from\r\n";
    $headers .= "Reply-To: $from\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

/**
 * Send order confirmation email
 */
function sendOrderConfirmation($orderData, $userEmail) {
    $subject = "Order Confirmation - JackoTimespiece";
    $message = "
    <html>
    <head>
        <title>Order Confirmation</title>
    </head>
    <body>
        <h2>Thank you for your order!</h2>
        <p>Order ID: {$orderData['order_id']}</p>
        <p>Total: " . formatCurrency($orderData['total']) . "</p>
        <p>We'll notify you when your order ships.</p>
    </body>
    </html>";
    
    return sendEmail($userEmail, $subject, $message);
}

/**
 * Log activity
 */
function logActivity($userId, $action, $details = '', $conn) {
    $stmt = $conn->prepare('INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $stmt->bind_param('issss', $userId, $action, $details, $ip, $userAgent);
    return $stmt->execute();
}

/**
 * Get pagination data
 */
function getPagination($totalItems, $itemsPerPage, $currentPage) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    return [
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'items_per_page' => $itemsPerPage,
        'total_items' => $totalItems,
        'offset' => $offset,
        'has_previous' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
        'previous_page' => $currentPage - 1,
        'next_page' => $currentPage + 1
    ];
}

/**
 * Generate pagination HTML
 */
function generatePaginationHTML($pagination, $baseUrl) {
    if ($pagination['total_pages'] <= 1) {
        return '';
    }
    
    $html = '<div class="pagination flex justify-center gap-2 mt-8">';
    
    // Previous button
    if ($pagination['has_previous']) {
        $html .= '<a href="' . $baseUrl . '?page=' . $pagination['previous_page'] . '" class="px-4 py-2 bg-gold text-black rounded hover:bg-white transition">Previous</a>';
    }
    
    // Page numbers
    $start = max(1, $pagination['current_page'] - 2);
    $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $activeClass = $i === $pagination['current_page'] ? 'bg-gold text-black' : 'bg-gray-800 text-white hover:bg-gray-700';
        $html .= '<a href="' . $baseUrl . '?page=' . $i . '" class="px-4 py-2 rounded transition ' . $activeClass . '">' . $i . '</a>';
    }
    
    // Next button
    if ($pagination['has_next']) {
        $html .= '<a href="' . $baseUrl . '?page=' . $pagination['next_page'] . '" class="px-4 py-2 bg-gold text-black rounded hover:bg-white transition">Next</a>';
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * Get search suggestions
 */
function getSearchSuggestions($query, $conn, $limit = 5) {
    $stmt = $conn->prepare('SELECT name, brand, model FROM watches WHERE (name LIKE ? OR brand LIKE ? OR model LIKE ?) AND status = "active" LIMIT ?');
    $searchTerm = '%' . $query . '%';
    $stmt->bind_param('sssi', $searchTerm, $searchTerm, $searchTerm, $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Calculate average rating
 */
function calculateAverageRating($ratings) {
    if (empty($ratings)) {
        return 0;
    }
    return array_sum($ratings) / count($ratings);
}

/**
 * Format rating stars
 */
function formatRatingStars($rating) {
    $fullStars = floor($rating);
    $halfStar = $rating - $fullStars >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
    
    $stars = '';
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '<i class="fas fa-star text-gold"></i>';
    }
    if ($halfStar) {
        $stars .= '<i class="fas fa-star-half-alt text-gold"></i>';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '<i class="far fa-star text-gray-400"></i>';
    }
    
    return $stars;
}

/**
 * Get time ago
 */
function getTimeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) {
        return 'Just now';
    } elseif ($time < 3600) {
        $minutes = floor($time / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($time < 86400) {
        $hours = floor($time / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($time < 2592000) {
        $days = floor($time / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return formatDate($datetime);
    }
}

/**
 * Clean URL slug
 */
function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

/**
 * Get setting value
 */
function getSetting($key, $conn, $default = '') {
    $stmt = $conn->prepare('SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1');
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['setting_value'] : $default;
}

/**
 * Update setting value
 */
function updateSetting($key, $value, $conn) {
    $stmt = $conn->prepare('INSERT INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()');
    $stmt->bind_param('sss', $key, $value, $value);
    return $stmt->execute();
}
?> 