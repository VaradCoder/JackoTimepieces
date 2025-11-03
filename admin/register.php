<?php
session_start();
require_once '../core/db/connection.php';

// Check if user is already logged in as admin
if (isset($_SESSION['admin_user'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $admin_code = $_POST['admin_code'] ?? '';

    // Validate admin code (you can change this to your preferred method)
    $valid_admin_codes = ['JACKO2024', 'ADMIN2024', 'SUPER2024'];
    
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (!in_array($admin_code, $valid_admin_codes)) {
        $error = 'Invalid admin registration code.';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM admin_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'An admin account with this email already exists.';
        } else {
            // Create admin user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admin_users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, 'admin')");
            $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $success = 'Admin account created successfully! You can now login.';
                // Clear form data
                $_POST = array();
            } else {
                $error = 'Failed to create admin account. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - JackoTimespiece</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --gold: #c9b37e; }
        .text-gold { color: var(--gold) !important; }
        .bg-gold { background: var(--gold) !important; }
        .border-gold { border-color: var(--gold) !important; }
        body { background: #000; }
    </style>
</head>
<body class="bg-black text-white min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto p-6">
        <div class="bg-gray-900 rounded-lg shadow-2xl p-8 border border-gray-800">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gold mb-2">
                    <span class="text-gold">Jacko</span><span class="text-white">Timespiece</span>
                </h1>
                <p class="text-gray-400">Admin Registration</p>
            </div>

            <!-- Error/Success Messages -->
            <?php if ($error): ?>
                <div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-900 border border-green-700 text-green-200 px-4 py-3 rounded mb-6">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-300 mb-2">
                            First Name
                        </label>
                        <input type="text" id="first_name" name="first_name" required
                               value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-300 mb-2">
                            Last Name
                        </label>
                        <input type="text" id="last_name" name="last_name" required
                               value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                        Email Address
                    </label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        Password
                    </label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-300 mb-2">
                        Confirm Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                           class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">
                </div>

                <div>
                    <label for="admin_code" class="block text-sm font-medium text-gray-300 mb-2">
                        Admin Registration Code
                    </label>
                    <input type="password" id="admin_code" name="admin_code" required
                           class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold"
                           placeholder="Enter admin code">
                    <p class="text-xs text-gray-500 mt-1">Contact the super admin for the registration code.</p>
                </div>

                <button type="submit" 
                        class="w-full bg-gold text-black font-semibold py-3 px-4 rounded-lg hover:bg-white hover:text-gold transition-colors duration-300">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Admin Account
                </button>
            </form>

            <!-- Login Link -->
            <div class="text-center mt-6">
                <p class="text-gray-400">
                    Already have an admin account? 
                    <a href="login.php" class="text-gold hover:text-white transition-colors duration-300">
                        Login here
                    </a>
                </p>
            </div>

            <!-- Back to Site -->
            <div class="text-center mt-4">
                <a href="../public/index.php" class="text-gray-500 hover:text-gold transition-colors duration-300 text-sm">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back to Website
                </a>
            </div>
        </div>
    </div>

    <script>
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            // You can add visual feedback here
        });
    </script>
</body>
</html> 