<?php
/**
 * Admin Login Page for JackoTimespiece
 * Handles admin authentication and login
 */

require_once '../core/config/app.php';
require_once '../core/db/connection.php';
require_once '../core/helpers/auth.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in as admin
if (isAdmin()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid request');
        }
        
        // Validate required fields
        if (empty($_POST['email']) || empty($_POST['password'])) {
            throw new Exception('Email and password are required');
        }
        
        // Sanitize input
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password']; // Don't sanitize password
        
        // Connect to database
        $conn = getConnection();
        
        // Attempt admin authentication
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE email = ? AND is_active = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Invalid email or password');
        }
        
        $admin = $result->fetch_assoc();
        
        if (!password_verify($password, $admin['password'])) {
            throw new Exception('Invalid email or password');
        }
        
        // Set admin session
        $_SESSION['admin_user'] = $admin;
        
        // Log successful login
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, 'admin_login', 'Admin logged in', ?)");
        $stmt->bind_param("is", $admin['id'], $_SERVER['REMOTE_ADDR']);
        $stmt->execute();
        
        // Redirect to admin dashboard
        header('Location: index.php');
        exit;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$pageTitle = "Admin Login";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - JackoTimespiece</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .bg-gold { background-color: #c9b37e; }
        .text-gold { color: #c9b37e; }
        .border-gold { border-color: #c9b37e; }
        .hover\:bg-gold:hover { background-color: #c9b37e; }
        .focus\:border-gold:focus { border-color: #c9b37e; }
        .focus\:ring-gold:focus { --tw-ring-color: #c9b37e; }
    </style>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <!-- Logo and Title -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-gold rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-clock text-black text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-white">JackoTimespiece</h2>
            <p class="text-gray-400 mt-2">Admin Panel</p>
        </div>

        <!-- Login Form -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-8">
            <div class="text-center mb-6">
                <h3 class="text-xl font-medium text-white">Sign in to your account</h3>
                <p class="text-gray-400 mt-2">Enter your credentials to access the admin panel</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-900 border border-green-700 text-green-200 px-4 py-3 rounded mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm"><?php echo htmlspecialchars($success); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               required
                               class="block w-full pl-10 pr-3 py-3 border border-gray-600 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition"
                               placeholder="Enter your email"
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required
                               class="block w-full pl-10 pr-10 py-3 border border-gray-600 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition"
                               placeholder="Enter your password">
                        <button type="button" 
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i id="password-toggle" class="fas fa-eye text-gray-400 hover:text-gray-300 cursor-pointer"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="remember" 
                               name="remember"
                               class="h-4 w-4 text-gold focus:ring-gold border-gray-600 rounded bg-gray-700">
                        <label for="remember" class="ml-2 block text-sm text-gray-300">
                            Remember me
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-black bg-gold hover:bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold transition">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-black group-hover:text-gray-700"></i>
                        </span>
                        Sign in
                    </button>
                </div>
            </form>

            <!-- Back to Store -->
            <div class="mt-6 text-center">
                <a href="../public/index.php" class="text-gold hover:text-white transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Store
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center">
            <p class="text-gray-500 text-sm">
                &copy; <?php echo date('Y'); ?> JackoTimespiece. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            }
        }

        // Auto-focus on email field
        document.getElementById('email').focus();
    </script>
</body>
</html> 