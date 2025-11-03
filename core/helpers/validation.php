<?php
/**
 * Validation Helper Functions for JackoTimespiece
 * Handles form validation, data sanitization, and input verification
 */

/**
 * Validate required fields
 */
function validateRequired($data, $fields) {
    $errors = [];
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    return $errors;
}

/**
 * Validate email format
 */
function validateEmail($email) {
    if (empty($email)) {
        return ['valid' => false, 'error' => 'Email is required'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'error' => 'Invalid email format'];
    }
    
    return ['valid' => true];
}

/**
 * Validate phone number (Indian format)
 */
function validatePhone($phone) {
    if (empty($phone)) {
        return ['valid' => false, 'error' => 'Phone number is required'];
    }
    
    // Remove spaces and special characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    if (strlen($phone) !== 10) {
        return ['valid' => false, 'error' => 'Phone number must be 10 digits'];
    }
    
    if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
        return ['valid' => false, 'error' => 'Invalid phone number format'];
    }
    
    return ['valid' => true, 'phone' => $phone];
}

/**
 * Validate password strength
 */
function validatePassword($password) {
    if (empty($password)) {
        return ['valid' => false, 'error' => 'Password is required'];
    }
    
    if (strlen($password) < 8) {
        return ['valid' => false, 'error' => 'Password must be at least 8 characters long'];
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        return ['valid' => false, 'error' => 'Password must contain at least one uppercase letter'];
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        return ['valid' => false, 'error' => 'Password must contain at least one lowercase letter'];
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        return ['valid' => false, 'error' => 'Password must contain at least one number'];
    }
    
    return ['valid' => true];
}

/**
 * Validate password confirmation
 */
function validatePasswordConfirmation($password, $confirmPassword) {
    if (empty($confirmPassword)) {
        return ['valid' => false, 'error' => 'Please confirm your password'];
    }
    
    if ($password !== $confirmPassword) {
        return ['valid' => false, 'error' => 'Passwords do not match'];
    }
    
    return ['valid' => true];
}

/**
 * Validate username
 */
function validateUsername($username) {
    if (empty($username)) {
        return ['valid' => false, 'error' => 'Username is required'];
    }
    
    if (strlen($username) < 3) {
        return ['valid' => false, 'error' => 'Username must be at least 3 characters long'];
    }
    
    if (strlen($username) > 20) {
        return ['valid' => false, 'error' => 'Username must be less than 20 characters'];
    }
    
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return ['valid' => false, 'error' => 'Username can only contain letters, numbers, and underscores'];
    }
    
    return ['valid' => true];
}

/**
 * Validate name
 */
function validateName($name, $fieldName = 'Name') {
    if (empty($name)) {
        return ['valid' => false, 'error' => $fieldName . ' is required'];
    }
    
    if (strlen($name) < 2) {
        return ['valid' => false, 'error' => $fieldName . ' must be at least 2 characters long'];
    }
    
    if (strlen($name) > 50) {
        return ['valid' => false, 'error' => $fieldName . ' must be less than 50 characters'];
    }
    
    if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        return ['valid' => false, 'error' => $fieldName . ' can only contain letters and spaces'];
    }
    
    return ['valid' => true];
}

/**
 * Validate address
 */
function validateAddress($address) {
    if (empty($address)) {
        return ['valid' => false, 'error' => 'Address is required'];
    }
    
    if (strlen($address) < 10) {
        return ['valid' => false, 'error' => 'Address must be at least 10 characters long'];
    }
    
    if (strlen($address) > 200) {
        return ['valid' => false, 'error' => 'Address must be less than 200 characters'];
    }
    
    return ['valid' => true];
}

/**
 * Validate city
 */
function validateCity($city) {
    if (empty($city)) {
        return ['valid' => false, 'error' => 'City is required'];
    }
    
    if (strlen($city) < 2) {
        return ['valid' => false, 'error' => 'City must be at least 2 characters long'];
    }
    
    if (strlen($city) > 50) {
        return ['valid' => false, 'error' => 'City must be less than 50 characters'];
    }
    
    return ['valid' => true];
}

/**
 * Validate state
 */
function validateState($state) {
    if (empty($state)) {
        return ['valid' => false, 'error' => 'State is required'];
    }
    
    if (strlen($state) < 2) {
        return ['valid' => false, 'error' => 'State must be at least 2 characters long'];
    }
    
    if (strlen($state) > 50) {
        return ['valid' => false, 'error' => 'State must be less than 50 characters'];
    }
    
    return ['valid' => true];
}

/**
 * Validate ZIP code (Indian PIN code)
 */
function validateZipCode($zipCode) {
    if (empty($zipCode)) {
        return ['valid' => false, 'error' => 'ZIP code is required'];
    }
    
    // Remove spaces
    $zipCode = preg_replace('/\s/', '', $zipCode);
    
    if (!preg_match('/^[1-9][0-9]{5}$/', $zipCode)) {
        return ['valid' => false, 'error' => 'Invalid ZIP code format'];
    }
    
    return ['valid' => true, 'zip_code' => $zipCode];
}

/**
 * Validate price
 */
function validatePrice($price) {
    if (empty($price)) {
        return ['valid' => false, 'error' => 'Price is required'];
    }
    
    if (!is_numeric($price)) {
        return ['valid' => false, 'error' => 'Price must be a number'];
    }
    
    if ($price <= 0) {
        return ['valid' => false, 'error' => 'Price must be greater than 0'];
    }
    
    if ($price > 999999999) {
        return ['valid' => false, 'error' => 'Price is too high'];
    }
    
    return ['valid' => true, 'price' => floatval($price)];
}

/**
 * Validate quantity
 */
function validateQuantity($quantity) {
    if (empty($quantity)) {
        return ['valid' => false, 'error' => 'Quantity is required'];
    }
    
    if (!is_numeric($quantity)) {
        return ['valid' => false, 'error' => 'Quantity must be a number'];
    }
    
    if ($quantity <= 0) {
        return ['valid' => false, 'error' => 'Quantity must be greater than 0'];
    }
    
    if ($quantity > 100) {
        return ['valid' => false, 'error' => 'Quantity cannot exceed 100'];
    }
    
    return ['valid' => true, 'quantity' => intval($quantity)];
}

/**
 * Validate file upload
 */
function validateFileUpload($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'], $maxSize = 5242880) {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['valid' => false, 'error' => 'No file uploaded'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds maximum size limit',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form size limit',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        return ['valid' => false, 'error' => $errors[$file['error']] ?? 'Unknown upload error'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['valid' => false, 'error' => 'File too large. Maximum size is ' . ($maxSize / 1024 / 1024) . 'MB'];
    }
    
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedTypes)) {
        return ['valid' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes)];
    }
    
    return ['valid' => true];
}

/**
 * Validate image dimensions
 */
function validateImageDimensions($file, $minWidth = 100, $minHeight = 100, $maxWidth = 5000, $maxHeight = 5000) {
    $imageInfo = getimagesize($file['tmp_name']);
    if (!$imageInfo) {
        return ['valid' => false, 'error' => 'Invalid image file'];
    }
    
    $width = $imageInfo[0];
    $height = $imageInfo[1];
    
    if ($width < $minWidth || $height < $minHeight) {
        return ['valid' => false, 'error' => "Image dimensions must be at least {$minWidth}x{$minHeight} pixels"];
    }
    
    if ($width > $maxWidth || $height > $maxHeight) {
        return ['valid' => false, 'error' => "Image dimensions must not exceed {$maxWidth}x{$maxHeight} pixels"];
    }
    
    return ['valid' => true, 'width' => $width, 'height' => $height];
}

/**
 * Validate date
 */
function validateDate($date, $format = 'Y-m-d') {
    if (empty($date)) {
        return ['valid' => false, 'error' => 'Date is required'];
    }
    
    $d = DateTime::createFromFormat($format, $date);
    if (!$d || $d->format($format) !== $date) {
        return ['valid' => false, 'error' => 'Invalid date format'];
    }
    
    return ['valid' => true, 'date' => $date];
}

/**
 * Validate future date
 */
function validateFutureDate($date, $format = 'Y-m-d') {
    $dateValidation = validateDate($date, $format);
    if (!$dateValidation['valid']) {
        return $dateValidation;
    }
    
    $inputDate = DateTime::createFromFormat($format, $date);
    $today = new DateTime();
    
    if ($inputDate <= $today) {
        return ['valid' => false, 'error' => 'Date must be in the future'];
    }
    
    return ['valid' => true, 'date' => $date];
}

/**
 * Validate coupon code
 */
function validateCouponCode($code) {
    if (empty($code)) {
        return ['valid' => false, 'error' => 'Coupon code is required'];
    }
    
    if (strlen($code) < 3) {
        return ['valid' => false, 'error' => 'Coupon code must be at least 3 characters long'];
    }
    
    if (strlen($code) > 20) {
        return ['valid' => false, 'error' => 'Coupon code must be less than 20 characters'];
    }
    
    if (!preg_match('/^[A-Z0-9]+$/', strtoupper($code))) {
        return ['valid' => false, 'error' => 'Coupon code can only contain letters and numbers'];
    }
    
    return ['valid' => true, 'code' => strtoupper($code)];
}

/**
 * Validate order data
 */
function validateOrderData($orderData) {
    $errors = [];
    
    // Validate customer info
    $customerFields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'state', 'zip'];
    foreach ($customerFields as $field) {
        if (empty($orderData['customer_info'][$field])) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    
    // Validate email format
    if (!empty($orderData['customer_info']['email'])) {
        $emailValidation = validateEmail($orderData['customer_info']['email']);
        if (!$emailValidation['valid']) {
            $errors['email'] = $emailValidation['error'];
        }
    }
    
    // Validate phone format
    if (!empty($orderData['customer_info']['phone'])) {
        $phoneValidation = validatePhone($orderData['customer_info']['phone']);
        if (!$phoneValidation['valid']) {
            $errors['phone'] = $phoneValidation['error'];
        }
    }
    
    // Validate payment info
    if (empty($orderData['payment_info']['total']) || $orderData['payment_info']['total'] <= 0) {
        $errors['total'] = 'Invalid order total';
    }
    
    // Validate cart items
    if (empty($orderData['cart_items'])) {
        $errors['cart'] = 'Cart is empty';
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    
    return $data;
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    if (empty($token)) {
        return ['valid' => false, 'error' => 'CSRF token is required'];
    }
    
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return ['valid' => false, 'error' => 'Invalid CSRF token'];
    }
    
    return ['valid' => true];
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate search query
 */
function validateSearchQuery($query) {
    if (empty($query)) {
        return ['valid' => false, 'error' => 'Search query is required'];
    }
    
    if (strlen($query) < 2) {
        return ['valid' => false, 'error' => 'Search query must be at least 2 characters long'];
    }
    
    if (strlen($query) > 100) {
        return ['valid' => false, 'error' => 'Search query must be less than 100 characters'];
    }
    
    return ['valid' => true, 'query' => $query];
}
?> 