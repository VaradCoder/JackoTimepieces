<?php
session_start();
require_once __DIR__ . '/../core/config/constants.php';
require_once __DIR__ . '/../core/db/connection.php';

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    header('Location: ' . ACCOUNT_INDEX);
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (!$email || !$password) {
        $error = 'Please enter both email and password.';
    } else {
        try {
            $stmt = $conn->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if ($user && password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'image' => $user['profile_pic'] ?? 'default.png',
                    'is_admin' => $user['is_admin'] ?? 0
                ];
                
                // Set remember me cookie if requested
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 days
                    
                    // Store token in database (you might want to add a remember_tokens table)
                    // For now, we'll just set the session
                }
                
                // Redirect based on user type
                if ($user['is_admin']) {
                    header('Location: ../admin/');
                } else {
                    header('Location: ' . ACCOUNT_INDEX);
                }
                exit;
            } else {
                $error = 'Invalid email or password. Please try again.';
            }
            $stmt->close();
        } catch (Exception $e) {
            $error = 'Login failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | JackoTimespiece</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%);
            min-height: 100vh;
        }
        .glass {
            background: rgba(24,24,24,0.85);
            backdrop-filter: blur(16px);
            border-radius: 2rem;
            box-shadow: 0 8px 40px 0 #c9b37e22, 0 1.5px 0 #c9b37e44;
            border: 2.5px solid #c9b37e;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .glass:before {
            content: '';
            position: absolute;
            top: -60%; left: -60%;
            width: 220%; height: 220%;
            background: conic-gradient(from 180deg at 50% 50%, #c9b37e33 0deg, #c9b37e 90deg, #c9b37e33 180deg, #c9b37e 270deg, #c9b37e33 360deg);
            filter: blur(32px);
            z-index: 0;
            animation: borderSpin 6s linear infinite;
        }
        @keyframes borderSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .glass > * { position: relative; z-index: 1; }
        .input {
            width: 100%;
            margin-bottom: 1.2rem;
            padding: 0.85rem 1.1rem;
            color: #fff;
            border-radius: 0.75rem;
            border: 1.5px solid #c9b37e;
            background: rgba(24,24,24,0.9);
            font-size: 1.08rem;
            transition: all 0.3s ease;
            outline: none;
        }
        .input:focus {
            border-color: #c9b37e;
            box-shadow: 0 0 0 3px #c9b37e33;
            background: rgba(35,35,35,0.9);
            transform: translateY(-2px);
        }
        .input::placeholder {
            color: #888;
        }
        .btn-gold {
            background: linear-gradient(135deg, #c9b37e 0%, #d4c08a 100%);
            color: #000;
            font-weight: 600;
            border-radius: 999px;
            padding: 1rem 0;
            font-size: 1.1rem;
            box-shadow: 0 4px 20px #c9b37e55;
            transition: all 0.3s ease;
            width: 100%;
            border: none;
            cursor: pointer;
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
            color: #c9b37e;
            box-shadow: 0 8px 30px #c9b37e99;
            transform: translateY(-2px);
        }
        .btn-gold:active {
            transform: translateY(0);
        }
        .logo-glow {
            font-size: 2.5rem;
            color: #c9b37e;
            text-shadow: 0 0 20px #c9b37e88, 0 2px 0 #fff2;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .glass .text-gold { color: #c9b37e; }
        .glass .text-link { 
            color: #c9b37e; 
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .glass .text-link:hover { 
            color: #fff; 
            text-shadow: 0 0 10px #c9b37e;
        }
        .error-message {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: center;
            border: 1px solid #ef4444;
        }
        .form-group {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #c9b37e;
            cursor: pointer;
            font-size: 1.2rem;
        }
        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        .checkbox-container input[type="checkbox"] {
            margin-right: 0.5rem;
            accent-color: #c9b37e;
            transform: scale(1.2);
        }
        .checkbox-container label {
            color: #ccc;
            font-size: 0.9rem;
        }
        .forgot-password {
            text-align: right;
            margin-bottom: 1rem;
        }
        .forgot-password a {
            color: #c9b37e;
            font-size: 0.9rem;
            text-decoration: none;
        }
        .forgot-password a:hover {
            color: #fff;
            text-shadow: 0 0 10px #c9b37e;
        }
        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: #666;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #333;
        }
        .divider span {
            padding: 0 1rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="text-white font-sans min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md glass p-8">
        <div class="logo-glow">
            <i class="fa-solid fa-crown"></i> 
            <span class="font-serif">JackoTimespiece</span>
        </div>
        
        <h1 class="text-3xl font-serif text-gold mb-6 text-center">Welcome Back</h1>
        
        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="loginForm">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" class="input" 
                       value="<?= htmlspecialchars($email) ?>" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" id="password" placeholder="Password" class="input" required>
                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            
            <div class="forgot-password">
                <a href="#" onclick="alert('Password reset functionality coming soon!')">Forgot Password?</a>
            </div>
            
            <div class="checkbox-container">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me for 30 days</label>
            </div>
            
            <button type="submit" class="btn-gold">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Sign In
            </button>
        </form>
        
        <div class="divider">
            <span>or</span>
        </div>
        
        <div class="text-center">
            <p class="text-gray-400 mb-4">Don't have an account?</p>
            <a href="<?= REGISTER_PAGE ?>" class="btn-gold" style="display: inline-block; text-decoration: none; padding: 0.75rem 2rem;">
                <i class="fas fa-user-plus mr-2"></i>
                Create Account
            </a>
        </div>
        
        <div class="mt-6 text-center text-sm text-gray-500">
            By signing in, you agree to our 
            <a href="<?= TERMS_PAGE ?>" class="text-link">Terms of Service</a> and 
            <a href="<?= PRIVACY_PAGE ?>" class="text-link">Privacy Policy</a>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }
        
        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.querySelector('input[name="email"]').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all fields!');
                return false;
            }
            
            // Basic email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address!');
                return false;
            }
        });
        
        // Auto-focus on email field
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.querySelector('input[name="email"]');
            if (emailInput && !emailInput.value) {
                emailInput.focus();
            }
        });
    </script>
</body>
</html> 