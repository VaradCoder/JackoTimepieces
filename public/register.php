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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    
    // Validation
    if (!$name || !$email || !$password || !$confirm_password) {
        $error = 'All required fields must be filled.';
    } elseif (strlen($name) < 2 || strlen($name) > 50) {
        $error = 'Name must be between 2 and 50 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif ($phone && !preg_match('/^[\+]?[1-9][\d]{0,15}$/', $phone)) {
        $error = 'Please enter a valid phone number.';
    } else {
        try {
            // Check if email already exists
            $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $error = 'Email already registered. Please use a different email or login.';
            } else {
                // Hash password and insert user
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $defaultPic = 'default.png';
                
                $stmt = $conn->prepare('INSERT INTO users (name, email, password, phone, profile_pic) VALUES (?, ?, ?, ?, ?)');
                if (!$stmt) {
                    throw new Exception('Database error: ' . $conn->error);
                }
                $stmt->bind_param('sssss', $name, $email, $hash, $phone, $defaultPic);
                
                if ($stmt->execute()) {
                    $user_id = $stmt->insert_id;
                    
                    // Set session
                    $_SESSION['user'] = [
                        'id' => $user_id,
                        'name' => $name,
                        'email' => $email,
                        'image' => $defaultPic,
                        'is_admin' => 0
                    ];
                    
                    $success = 'Registration successful! Welcome to JackoTimespiece.';
                    
                    // Redirect after a short delay
                    header('refresh:2;url=' . ACCOUNT_INDEX);
                } else {
                    throw new Exception('Failed to create account. Please try again.');
                }
            }
            $stmt->close();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | JackoTimespiece</title>
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
        .success-message {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: center;
            border: 1px solid #10b981;
        }
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 0.5rem;
            transition: all 0.3s ease;
        }
        .strength-weak { background: #dc2626; }
        .strength-medium { background: #f59e0b; }
        .strength-strong { background: #10b981; }
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
    </style>
</head>
<body class="text-white font-sans min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md glass p-8">
        <div class="logo-glow">
            <i class="fa-solid fa-crown"></i> 
            <span class="font-serif">JackoTimespiece</span>
        </div>
        
        <h1 class="text-3xl font-serif text-gold mb-6 text-center">Create Account</h1>
        
        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message">
                <i class="fas fa-check-circle mr-2"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="registerForm">
            <div class="form-group">
                <input type="text" name="name" placeholder="Full Name *" class="input" 
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address *" class="input" 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <input type="tel" name="phone" placeholder="Phone Number (Optional)" class="input" 
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <input type="password" name="password" id="password" placeholder="Password *" class="input" required>
                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                    <i class="fas fa-eye"></i>
                </button>
                <div class="password-strength" id="passwordStrength"></div>
            </div>
            
            <div class="form-group">
                <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm Password *" class="input" required>
                <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            
            <button type="submit" class="btn-gold mt-4">
                <i class="fas fa-user-plus mr-2"></i>
                Create Account
            </button>
        </form>
        
        <div class="mt-6 text-center text-gray-400">
            Already have an account? 
            <a href="<?= LOGIN_PAGE ?>" class="text-link">Sign In</a>
        </div>
        
        <div class="mt-4 text-center text-sm text-gray-500">
            By creating an account, you agree to our 
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
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            strengthBar.className = 'password-strength';
            if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 3) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        });
        
        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });
    </script>
</body>
</html> 