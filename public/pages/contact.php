<?php
session_start();
require_once __DIR__ . '/../../core/config/constants.php';
// require_once __DIR__ . '/../../templates/header.php';

$success_message = '';
$error_message = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // TODO: Implement actual email sending or database storage
        // For now, just show success message
        $success_message = 'Thank you for your message! We will get back to you within 24 hours.';
        
        // Clear form data
        $name = $email = $subject = $message = '';
    }
}
?>


<?php
// require_once __DIR__ . '/../core/config/constants.php';
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>JackoTimespiece</title>

  <!-- CDN Links -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../../assets/css/style.css">

  <style>
    :root {
      --gold: #c9b37e;
      --cream: #f5f5e6;
    }
    .text-gold { color: var(--gold) !important; }
    .bg-gold { background: var(--gold) !important; }
    .border-gold { border-color: var(--gold) !important; }
    .logo-shine {
      position: relative;
      overflow: hidden;
    }
    .logo-shine::after {
      content: '';
      position: absolute;
      top: 0; left: -75%;
      width: 50%; height: 100%;
      background: linear-gradient(120deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.4) 50%, rgba(255,255,255,0) 100%);
      transform: skewX(-20deg);
      animation: shine 2.5s infinite;
    }
    @keyframes shine {
      0% { left: -75%; }
      60% { left: 120%; }
      100% { left: 120%; }
    }
    header {
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(201,179,126,0.08);
    }
    .nav-link {
      position: relative;
      transition: color 0.2s;
    }
    .nav-link:hover, .nav-link.active {
      color: var(--gold) !important;
    }
    .fa-heart, .fa-shopping-cart, .fa-user-circle {
      transition: color 0.2s, transform 0.2s;
    }
    .fa-heart:hover, .fa-shopping-cart:hover, .fa-user-circle:hover {
      color: var(--gold) !important;
      transform: scale(1.1);
    }
    .bg-[#0c0c0c] {
      background: #0c0c0c !important;
    }
    @media (max-width: 768px) {
      header .container { flex-direction: row; align-items: center; }
      nav, .hidden.md\:flex { display: none !important; }
      #mobile-menu { display: flex !important; }
      #mobile-menu a { font-size: 1rem; }
    }
    /* Gold border for profile image */
    .border-gold { border: 2px solid var(--gold) !important; }
    /* Mobile nav custom (if you add it later) */
    #mobile-menu { transition: max-height 0.3s, opacity 0.3s; }
  </style>
</head>
<body class="bg-black text-white font-sans">

<header class="bg-[#0c0c0c] shadow-md sticky top-0 z-50">
  <div class="container mx-auto px-6 py-4 flex items-center justify-between">
    <!-- Left: Logo -->
    <a href="../index.php" class="text-2xl md:text-3xl font-bold">
      <span class="text-gold">Jacko</span><span class="text-white">Timespiece</span>
    </a>

    <!-- Hamburger (Mobile) -->
    <button id="mobile-menu-toggle" class="md:hidden text-2xl text-gold focus:outline-none ml-2">
      <i class="fa-solid fa-bars"></i>
    </button>

    <!-- Middle: Navigation -->
    <nav class="hidden md:flex gap-8 text-sm uppercase tracking-wide">
      <a href="../store.php" class="nav-link">Store</a>
      <a href="about.php" class="nav-link">About Us</a>
      <a href="contact.php" class="nav-link">Contact</a>
    </nav>

    <!-- Right: Icons -->
    <div class="hidden md:flex items-center gap-5 text-white">
      <a href="../account/wishlist.php"><i class="fa-solid fa-heart text-lg hover:text-gold"></i></a>
      <a href="../cart.php"<i class="fa-solid fa-shopping-cart text-lg hover:text-gold"></i></a>
      <?php if (isset($_SESSION['user'])): ?>
        <a href="../account/index.php">
          <img src="../assets/images/users/<?= $_SESSION['user']['image'] ?? 'default.png' ?>" class="w-8 h-8 rounded-full border border-gold" alt="Profile">
        </a>
      <?php else: ?>
        <a href="../login.php" class="px-3 py-1 bg-gold text-black rounded-md hover:opacity-90 transition">Login</a>
      <?php endif; ?>
    </div>
  </div>
  <!-- Mobile Nav -->
  <div id="mobile-menu" class="md:hidden hidden flex-col px-6 pb-4 space-y-3 bg-[#181818] border-t border-gold animate__animated animate__fadeInDown">
    <a href="../store.php" class="nav-link py-2">Store</a>
    <a href="about.php" class="nav-link py-2">About Us</a>
    <!-- <a href="<?= ABOUT_PAGE ?>" class="nav-link py-2">About Us</a> -->
    <a href="contact.php" class="nav-link py-2">Contact</a>
    <div class="flex items-center gap-4 pt-2">
      <a href="../account/wishlist.php"><i class="fa-solid fa-heart text-lg hover:text-gold"></i></a>
      <a href="../cart.php"><i class="fa-solid fa-shopping-cart text-lg hover:text-gold"></i></a>
      <?php if (isset($_SESSION['user'])): ?>
        <a href="../account/index.php">
          <img src="../assets/images/users/<?= $_SESSION['user']['image'] ?? 'default.png' ?>" class="w-8 h-8 rounded-full border border-gold" alt="Profile">
        </a>
      <?php else: ?>
        <a href="../login.php" class="px-3 py-1 bg-gold text-black rounded-md hover:opacity-90 transition">Login</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<script>
  // Mobile menu toggle
  document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('mobile-menu-toggle');
    const menu = document.getElementById('mobile-menu');
    if (toggle && menu) {
      toggle.addEventListener('click', function() {
        menu.classList.toggle('hidden');
      });
    }
  });
</script>
<style>
  @media (max-width: 768px) {
    header .container { flex-direction: row; align-items: center; }
    nav, .hidden.md\:flex { display: none !important; }
    #mobile-menu { display: flex !important; }
    #mobile-menu a { font-size: 1rem; }
  }
  #mobile-menu {
    transition: max-height 0.3s, opacity 0.3s;
    max-height: 400px;
    opacity: 1;
  }
  #mobile-menu.hidden {
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    display: none !important;
  }
</style>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | JackoTimespiece</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <style>
        :root { --gold: #c9b37e; --white: #fff; --black: #000; }
        .text-gold { color: var(--gold) !important; }
        .bg-gold { background: var(--gold) !important; }
        .border-gold { border-color: var(--gold) !important; }
        .font-serif { font-family: 'Playfair Display', serif; }
        .glass { 
            background: rgba(24,24,24,0.85); 
            backdrop-filter: blur(16px); 
            border-radius: 1.5rem; 
            box-shadow: 0 8px 40px 0 #c9b37e22, 0 1.5px 0 #c9b37e44;
            border: 2px solid #c9b37e;
        }
        .contact-hero {
            background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(201,179,126,0.1) 100%);
            position: relative;
            overflow: hidden;
        }
        .contact-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('/assets/images/brand/contact-bg.jpg') center/cover;
            opacity: 0.3;
            z-index: -1;
        }
        .form-input {
            width: 100%;
            padding: 1rem 1.2rem;
            background: rgba(24,24,24,0.9);
            border: 1.5px solid #c9b37e;
            border-radius: 0.75rem;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
        }
        .form-input:focus {
            border-color: #fff;
            box-shadow: 0 0 0 2px #c9b37e55;
            background: rgba(35,35,35,0.95);
        }
        .form-input::placeholder {
            color: #888;
        }
        .btn-contact {
            background: #c9b37e;
            color: #000;
            font-weight: 600;
            border-radius: 999px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            box-shadow: 0 0 16px #c9b37e55;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .btn-contact:hover {
            background: #fff;
            color: #c9b37e;
            box-shadow: 0 0 32px #c9b37e99;
            transform: scale(1.05);
        }
        .contact-card {
            background: rgba(24,24,24,0.9);
            border: 1.5px solid #c9b37e;
            border-radius: 1rem;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(201,179,126,0.2);
        }
        .contact-icon {
            width: 60px;
            height: 60px;
            background: #c9b37e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: #000;
            font-size: 1.5rem;
        }
        .map-container {
            border-radius: 1rem;
            overflow: hidden;
            border: 2px solid #c9b37e;
        }
    </style>
</head>
<body class="bg-black text-white font-sans">
    
    <!-- Hero Section -->
    <section class="contact-hero py-20 relative">
        <div class="container mx-auto px-6 text-center relative z-10">
            <h1 class="text-4xl md:text-6xl font-serif text-gold mb-6">Get in Touch</h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                Have questions about our luxury timepieces? We're here to help you find the perfect watch that tells your story.
            </p>
        </div>
    </section>

    <!-- Main Contact Section -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-12">
                
                <!-- Contact Form -->
                <div class="glass p-8">
                    <h2 class="text-3xl font-serif text-gold mb-8">Send us a Message</h2>
                    
                    <?php if ($success_message): ?>
                        <div class="bg-green-900 text-green-100 p-4 rounded-lg mb-6">
                            <?= htmlspecialchars($success_message) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="bg-red-900 text-red-100 p-4 rounded-lg mb-6">
                            <?= htmlspecialchars($error_message) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gold font-semibold mb-2">Full Name *</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>" 
                                       class="form-input" placeholder="Your full name" required>
                            </div>
                            <div>
                                <label class="block text-gold font-semibold mb-2">Email Address *</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" 
                                       class="form-input" placeholder="your.email@example.com" required>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gold font-semibold mb-2">Subject *</label>
                            <input type="text" name="subject" value="<?= htmlspecialchars($subject ?? '') ?>" 
                                   class="form-input" placeholder="What's this about?" required>
                        </div>
                        
                        <div>
                            <label class="block text-gold font-semibold mb-2">Message *</label>
                            <textarea name="message" rows="6" class="form-input" 
                                      placeholder="Tell us more about your inquiry..." required><?= htmlspecialchars($message ?? '') ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn-contact w-full">
                            <i class="fas fa-paper-plane mr-2"></i>Send Message
                        </button>
                    </form>
                </div>
                
                <!-- Contact Information -->
                <div class="space-y-8">
                    <div>
                        <h2 class="text-3xl font-serif text-gold mb-8">Contact Information</h2>
                        <p class="text-gray-300 text-lg mb-8">
                            We're here to help you find the perfect timepiece. Reach out to us through any of these channels.
                        </p>
                    </div>
                    
                    <!-- Contact Cards -->
                    <div class="space-y-6">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <h3 class="text-xl font-serif text-gold mb-2">Phone</h3>
                            <p class="text-gray-300 mb-2">+91 8160375699</p>
                            <p class="text-gray-300 mb-2">+91 9699164510</p>
                            <p class="text-sm text-gray-400">Monday - Friday: 12:20 AM - 8:00 PM IST</p>
                        </div>
                        
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h3 class="text-xl font-serif text-gold mb-2">Email</h3>
                            <p class="text-gray-300 mb-2">jackotimespiece@gmail.com</p>
                            <p class="text-sm text-gray-400">We respond within 24 hours</p>
                        </div>
                        
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <h3 class="text-xl font-serif text-gold mb-2">Location</h3>
                            <p class="text-gray-300 mb-2">Pune, Maharashtra, India</p>
                            <p class="text-sm text-gray-400">Visit our showroom by appointment</p>
                        </div>
                        
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3 class="text-xl font-serif text-gold mb-2">Business Hours</h3>
                            <p class="text-gray-300 mb-2">Monday - Friday: 9:00 AM - 6:00 PM</p>
                            <p class="text-gray-300 mb-2">Saturday: 10:00 AM - 4:00 PM</p>
                            <p class="text-sm text-gray-400">Sunday: Closed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-16 bg-[#0c0c0c]">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-serif text-gold mb-4">Find Us</h2>
                <p class="text-gray-300">Visit our showroom in Pune to experience our luxury timepieces in person.</p>
            </div>
            
            <div class="map-container max-w-4xl mx-auto">
                <!-- Replace with actual Google Maps embed -->
                <div class="bg-[#181818] h-96 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt text-6xl text-gold mb-4"></i>
                        <p class="text-gray-300 text-lg">Interactive Map Coming Soon</p>
                        <p class="text-gray-400 text-sm mt-2">Pune, Maharashtra, India</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-serif text-gold mb-4">Frequently Asked Questions</h2>
                <p class="text-gray-300">Quick answers to common questions about our services.</p>
            </div>
            
            <div class="max-w-3xl mx-auto space-y-6">
                <div class="glass p-6">
                    <h3 class="text-xl font-serif text-gold mb-3">How can I schedule a showroom appointment?</h3>
                    <p class="text-gray-300">Contact us via phone or email to schedule a private viewing. We offer personalized consultations to help you find the perfect timepiece.</p>
                </div>
                
                <div class="glass p-6">
                    <h3 class="text-xl font-serif text-gold mb-3">Do you offer international shipping?</h3>
                    <p class="text-gray-300">Yes, we offer worldwide shipping with secure packaging and insurance. Shipping costs and delivery times vary by location.</p>
                </div>
                
                <div class="glass p-6">
                    <h3 class="text-xl font-serif text-gold mb-3">What is your return policy?</h3>
                    <p class="text-gray-300">We offer a 30-day return policy for unworn watches in original condition. Custom and limited edition pieces may have different terms.</p>
                </div>
                
                <div class="glass p-6">
                    <h3 class="text-xl font-serif text-gold mb-3">Do you provide warranty and servicing?</h3>
                    <p class="text-gray-300">All our watches come with manufacturer warranty. We also offer professional servicing and maintenance through our authorized service center.</p>
                </div>
            </div>
        </div>
    </section>

    <?php require_once __DIR__ . '/../../templates/footer.php'; ?>

    <script>
        // Animate elements on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (window.anime) {
                // Hero animation
                anime({
                    targets: '.contact-hero h1, .contact-hero p',
                    opacity: [0, 1],
                    translateY: [30, 0],
                    delay: anime.stagger(200),
                    duration: 1000,
                    easing: 'easeOutCubic'
                });
                
                // Contact cards animation
                anime({
                    targets: '.contact-card',
                    opacity: [0, 1],
                    translateY: [40, 0],
                    delay: anime.stagger(150),
                    duration: 800,
                    easing: 'easeOutCubic'
                });
                
                // Form animation
                anime({
                    targets: '.glass form > *',
                    opacity: [0, 1],
                    translateX: [-20, 0],
                    delay: anime.stagger(100),
                    duration: 600,
                    easing: 'easeOutCubic'
                });
            }
        });
        
        // Form validation enhancement
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.style.borderColor = '#ef4444';
                        isValid = false;
                    } else {
                        field.style.borderColor = '#c9b37e';
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                }
            });
        }
    </script>
</body>
</html> 