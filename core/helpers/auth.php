<?php
/**
 * Authentication Helper Functions for JackoTimespiece
 * Handles user authentication, registration, and session management
 */

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

/**
 * Get current user data
 */
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user']['id'] ?? null;
}

/**
 * Authenticate user login
 */
function authenticateUser($email, $password, $conn) {
    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ? AND status = "active" LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && password_verify($password, $user['password'])) {
        // Create session data
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'role' => $user['role'],
            'image' => $user['image'] ?? 'default.png',
            'phone' => $user['phone'] ?? '',
            'address' => $user['address'] ?? '',
            'city' => $user['city'] ?? '',
            'state' => $user['state'] ?? '',
            'zip_code' => $user['zip_code'] ?? ''
        ];
        
        // Update last login
        $update_stmt = $conn->prepare('UPDATE users SET updated_at = NOW() WHERE id = ?');
        $update_stmt->bind_param('i', $user['id']);
        $update_stmt->execute();
        
        return ['success' => true, 'user' => $_SESSION['user']];
    }
    
    return ['success' => false, 'error' => 'Invalid email or password'];
}

/**
 * Register new user
 */
function registerUser($userData, $conn) {
    // Validate required fields
    $required = ['username', 'email', 'password', 'first_name', 'last_name'];
    foreach ($required as $field) {
        if (empty($userData[$field])) {
            return ['success' => false, 'error' => ucfirst($field) . ' is required'];
        }
    }
    
    // Check if email already exists
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $userData['email']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return ['success' => false, 'error' => 'Email already registered'];
    }
    
    // Check if username already exists
    $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $userData['username']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return ['success' => false, 'error' => 'Username already taken'];
    }
    
    // Hash password
    $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare('INSERT INTO users (username, email, password, first_name, last_name, phone, address, city, state, zip_code, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "customer", "active")');
    $stmt->bind_param('ssssssssss', 
        $userData['username'],
        $userData['email'],
        $hashedPassword,
        $userData['first_name'],
        $userData['last_name'],
        $userData['phone'] ?? '',
        $userData['address'] ?? '',
        $userData['city'] ?? '',
        $userData['state'] ?? '',
        $userData['zip_code'] ?? ''
    );
    
    if ($stmt->execute()) {
        $userId = $stmt->insert_id;
        
        // Create session for new user
        $_SESSION['user'] = [
            'id' => $userId,
            'username' => $userData['username'],
            'email' => $userData['email'],
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'role' => 'customer',
            'image' => 'default.png',
            'phone' => $userData['phone'] ?? '',
            'address' => $userData['address'] ?? '',
            'city' => $userData['city'] ?? '',
            'state' => $userData['state'] ?? '',
            'zip_code' => $userData['zip_code'] ?? ''
        ];
        
        return ['success' => true, 'user' => $_SESSION['user']];
    }
    
    return ['success' => false, 'error' => 'Registration failed. Please try again.'];
}

/**
 * Logout user
 */
function logoutUser() {
    session_destroy();
    return ['success' => true];
}

/**
 * Update user profile
 */
function updateUserProfile($userId, $userData, $conn) {
    $stmt = $conn->prepare('UPDATE users SET first_name = ?, last_name = ?, phone = ?, address = ?, city = ?, state = ?, zip_code = ?, updated_at = NOW() WHERE id = ?');
    $stmt->bind_param('sssssssi', 
        $userData['first_name'],
        $userData['last_name'],
        $userData['phone'] ?? '',
        $userData['address'] ?? '',
        $userData['city'] ?? '',
        $userData['state'] ?? '',
        $userData['zip_code'] ?? '',
        $userId
    );
    
    if ($stmt->execute()) {
        // Update session data
        $_SESSION['user']['first_name'] = $userData['first_name'];
        $_SESSION['user']['last_name'] = $userData['last_name'];
        $_SESSION['user']['phone'] = $userData['phone'] ?? '';
        $_SESSION['user']['address'] = $userData['address'] ?? '';
        $_SESSION['user']['city'] = $userData['city'] ?? '';
        $_SESSION['user']['state'] = $userData['state'] ?? '';
        $_SESSION['user']['zip_code'] = $userData['zip_code'] ?? '';
        
        return ['success' => true];
    }
    
    return ['success' => false, 'error' => 'Profile update failed'];
}

/**
 * Change user password
 */
function changePassword($userId, $currentPassword, $newPassword, $conn) {
    // Verify current password
    $stmt = $conn->prepare('SELECT password FROM users WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user || !password_verify($currentPassword, $user['password'])) {
        return ['success' => false, 'error' => 'Current password is incorrect'];
    }
    
    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?');
    $stmt->bind_param('si', $hashedPassword, $userId);
    
    if ($stmt->execute()) {
        return ['success' => true];
    }
    
    return ['success' => false, 'error' => 'Password change failed'];
}

/**
 * Upload and update user profile image
 */
function updateProfileImage($userId, $imageFile, $conn) {
    $uploadDir = __DIR__ . '/../../assets/images/users/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    // Validate file
    if (!in_array($imageFile['type'], $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.'];
    }
    
    if ($imageFile['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File too large. Maximum size is 5MB.'];
    }
    
    // Generate unique filename
    $extension = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
    $filename = 'user_' . $userId . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Upload file
    if (move_uploaded_file($imageFile['tmp_name'], $filepath)) {
        // Update database
        $stmt = $conn->prepare('UPDATE users SET image = ?, updated_at = NOW() WHERE id = ?');
        $stmt->bind_param('si', $filename, $userId);
        
        if ($stmt->execute()) {
            // Update session
            $_SESSION['user']['image'] = $filename;
            return ['success' => true, 'filename' => $filename];
        }
    }
    
    return ['success' => false, 'error' => 'Image upload failed'];
}

/**
 * Require authentication for protected pages
 */
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: ../public/login.php');
        exit;
    }
}

/**
 * Require admin access for admin pages
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ../public/login.php');
        exit;
    }
}

/**
 * Get user by ID
 */
function getUserById($userId, $conn) {
    $stmt = $conn->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Get all users (for admin)
 */
function getAllUsers($conn, $limit = 50, $offset = 0) {
    $stmt = $conn->prepare('SELECT id, username, email, first_name, last_name, role, status, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?');
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Delete user (for admin)
 */
function deleteUser($userId, $conn) {
    $stmt = $conn->prepare('DELETE FROM users WHERE id = ? AND role != "admin"');
    $stmt->bind_param('i', $userId);
    return $stmt->execute();
}
?> 