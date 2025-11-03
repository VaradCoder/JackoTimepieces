<?php
session_start();
require_once __DIR__ . '/../../core/config/constants.php';
// require_once __DIR__ . '/../../templates/header.php';
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
    <title>Terms of Service | JackoTimespiece</title>
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
        .terms-hero {
            background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(201,179,126,0.1) 100%);
            position: relative;
            overflow: hidden;
        }
        .terms-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('/assets/images/brand/terms-bg.jpg') center/cover;
            opacity: 0.3;
            z-index: -1;
        }
        .terms-section {
            background: rgba(24,24,24,0.9);
            border: 1.5px solid #c9b37e;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        .terms-section:hover {
            box-shadow: 0 5px 20px rgba(201,179,126,0.2);
        }
        .terms-section h3 {
            color: #c9b37e;
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid #c9b37e;
            padding-bottom: 0.5rem;
        }
        .terms-section p, .terms-section li {
            color: #d1d5db;
            line-height: 1.7;
            margin-bottom: 1rem;
        }
        .terms-section ul {
            list-style-type: disc;
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .terms-section ol {
            list-style-type: decimal;
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .last-updated {
            background: rgba(201,179,126,0.1);
            border: 1px solid #c9b37e;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 2rem;
            text-align: center;
        }
    </style>
</head>
<body class="bg-black text-white font-sans">
    
    <!-- Hero Section -->
    <section class="terms-hero py-20 relative">
        <div class="container mx-auto px-6 text-center relative z-10">
            <h1 class="text-4xl md:text-6xl font-serif text-gold mb-6">Terms of Service</h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                Please read these terms carefully before using our website and services. By using JackoTimespiece, you agree to these terms.
            </p>
        </div>
    </section>

    <!-- Terms of Service Content -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                
                <!-- Last Updated -->
                <div class="last-updated">
                    <p class="text-gold font-semibold">Last Updated: January 15, 2024</p>
                    <p class="text-gray-300 text-sm">These terms are effective as of the date listed above.</p>
                </div>

                <!-- Agreement -->
                <div class="terms-section">
                    <h3>Agreement to Terms</h3>
                    <p>
                        These Terms of Service ("Terms") govern your use of the JackoTimespiece website and services. By accessing or using our website, you agree to be bound by these Terms and our Privacy Policy.
                    </p>
                    <p>
                        If you do not agree to these Terms, please do not use our services. We reserve the right to modify these Terms at any time, and such modifications will be effective immediately upon posting.
                    </p>
                </div>

                <!-- Services Description -->
                <div class="terms-section">
                    <h3>Services Description</h3>
                    <p>JackoTimespiece provides:</p>
                    <ul>
                        <li>Online retail of luxury timepieces and accessories</li>
                        <li>Showroom consultations and appointments</li>
                        <li>Watch servicing and maintenance</li>
                        <li>Customer support and assistance</li>
                        <li>Educational content about luxury watches</li>
                    </ul>
                    <p>We reserve the right to modify, suspend, or discontinue any aspect of our services at any time.</p>
                </div>

                <!-- User Accounts -->
                <div class="terms-section">
                    <h3>User Accounts</h3>
                    <p>To access certain features of our website, you may need to create an account. You are responsible for:</p>
                    <ul>
                        <li>Providing accurate and complete information</li>
                        <li>Maintaining the security of your account credentials</li>
                        <li>All activities that occur under your account</li>
                        <li>Notifying us immediately of any unauthorized use</li>
                    </ul>
                    <p>We reserve the right to terminate accounts that violate these Terms or for any other reason at our discretion.</p>
                </div>

                <!-- Purchases and Payment -->
                <div class="terms-section">
                    <h3>Purchases and Payment</h3>
                    
                    <h4 class="text-gold font-semibold mt-4 mb-2">Order Acceptance</h4>
                    <p>All orders are subject to acceptance and availability. We reserve the right to refuse any order for any reason, including but not limited to:</p>
                    <ul>
                        <li>Product unavailability</li>
                        <li>Pricing errors</li>
                        <li>Suspicious or fraudulent activity</li>
                        <li>Violation of these Terms</li>
                    </ul>

                    <h4 class="text-gold font-semibold mt-4 mb-2">Pricing</h4>
                    <p>All prices are in Indian Rupees (₹) and include applicable taxes unless otherwise stated. Prices are subject to change without notice.</p>

                    <h4 class="text-gold font-semibold mt-4 mb-2">Payment</h4>
                    <p>We accept various payment methods including credit cards, debit cards, UPI, and digital wallets. Payment is processed securely through our payment partners.</p>
                </div>

                <!-- Shipping and Delivery -->
                <div class="terms-section">
                    <h3>Shipping and Delivery</h3>
                    <ul>
                        <li>Delivery times are estimates and may vary based on location and availability</li>
                        <li>Risk of loss and title transfer upon delivery to the carrier</li>
                        <li>Signature may be required for high-value items</li>
                        <li>International shipping subject to customs and import regulations</li>
                        <li>Shipping costs and taxes are the responsibility of the customer</li>
                    </ul>
                </div>

                <!-- Returns and Refunds -->
                <div class="terms-section">
                    <h3>Returns and Refunds</h3>
                    <p>We offer a 30-day return policy for most items, subject to the following conditions:</p>
                    <ul>
                        <li>Item must be unworn and in original condition</li>
                        <li>All original packaging and documentation included</li>
                        <li>Return authorization required</li>
                        <li>Custom and limited edition items may have different return terms</li>
                        <li>Return shipping costs may apply</li>
                    </ul>
                    <p>Refunds will be processed within 14 business days of receiving the returned item.</p>
                </div>

                <!-- Product Information -->
                <div class="terms-section">
                    <h3>Product Information</h3>
                    <p>While we strive to provide accurate product information:</p>
                    <ul>
                        <li>Product descriptions and images are for reference only</li>
                        <li>Actual products may vary slightly from images</li>
                        <li>Specifications are subject to change by manufacturers</li>
                        <li>We are not responsible for typographical errors</li>
                    </ul>
                    <p>We recommend contacting us for the most current product information.</p>
                </div>

                <!-- Intellectual Property -->
                <div class="terms-section">
                    <h3>Intellectual Property</h3>
                    <p>All content on this website, including but not limited to:</p>
                    <ul>
                        <li>Text, graphics, logos, and images</li>
                        <li>Product designs and descriptions</li>
                        <li>Website layout and functionality</li>
                        <li>Brand names and trademarks</li>
                    </ul>
                    <p>Is the property of JackoTimespiece or its licensors and is protected by copyright, trademark, and other intellectual property laws.</p>
                    <p>You may not reproduce, distribute, or create derivative works without our express written consent.</p>
                </div>

                <!-- Prohibited Uses -->
                <div class="terms-section">
                    <h3>Prohibited Uses</h3>
                    <p>You agree not to use our services to:</p>
                    <ul>
                        <li>Violate any applicable laws or regulations</li>
                        <li>Infringe on intellectual property rights</li>
                        <li>Transmit harmful, offensive, or inappropriate content</li>
                        <li>Attempt to gain unauthorized access to our systems</li>
                        <li>Interfere with website functionality</li>
                        <li>Engage in fraudulent or deceptive practices</li>
                        <li>Resell products without authorization</li>
                    </ul>
                </div>

                <!-- Limitation of Liability -->
                <div class="terms-section">
                    <h3>Limitation of Liability</h3>
                    <p>To the maximum extent permitted by law, JackoTimespiece shall not be liable for:</p>
                    <ul>
                        <li>Indirect, incidental, or consequential damages</li>
                        <li>Loss of profits, data, or business opportunities</li>
                        <li>Damages resulting from third-party actions</li>
                        <li>Issues beyond our reasonable control</li>
                    </ul>
                    <p>Our total liability shall not exceed the amount paid for the specific product or service.</p>
                </div>

                <!-- Warranty Disclaimers -->
                <div class="terms-section">
                    <h3>Warranty Disclaimers</h3>
                    <p>Our services are provided "as is" without warranties of any kind, either express or implied, including but not limited to:</p>
                    <ul>
                        <li>Merchantability</li>
                        <li>Fitness for a particular purpose</li>
                        <li>Non-infringement</li>
                        <li>Uninterrupted or error-free service</li>
                    </ul>
                    <p>Product warranties are provided by manufacturers and subject to their terms.</p>
                </div>

                <!-- Indemnification -->
                <div class="terms-section">
                    <h3>Indemnification</h3>
                    <p>You agree to indemnify and hold harmless JackoTimespiece, its officers, directors, employees, and agents from any claims, damages, or expenses arising from:</p>
                    <ul>
                        <li>Your use of our services</li>
                        <li>Your violation of these Terms</li>
                        <li>Your violation of any third-party rights</li>
                        <li>Any content you submit or transmit</li>
                    </ul>
                </div>

                <!-- Governing Law -->
                <div class="terms-section">
                    <h3>Governing Law and Dispute Resolution</h3>
                    <p>These Terms are governed by the laws of India. Any disputes shall be resolved through:</p>
                    <ol>
                        <li>Good faith negotiations between parties</li>
                        <li>Mediation if negotiations fail</li>
                        <li>Arbitration in Pune, Maharashtra, India</li>
                    </ol>
                    <p>You agree to submit to the jurisdiction of courts in Pune, Maharashtra, India.</p>
                </div>

                <!-- Severability -->
                <div class="terms-section">
                    <h3>Severability</h3>
                    <p>If any provision of these Terms is found to be unenforceable or invalid, the remaining provisions will continue in full force and effect. The unenforceable provision will be modified to the minimum extent necessary to make it enforceable.</p>
                </div>

                <!-- Entire Agreement -->
                <div class="terms-section">
                    <h3>Entire Agreement</h3>
                    <p>These Terms, together with our Privacy Policy, constitute the entire agreement between you and JackoTimespiece regarding your use of our services. Any prior agreements or understandings are superseded by these Terms.</p>
                </div>

                <!-- Contact Information -->
                <div class="terms-section">
                    <h3>Contact Information</h3>
                    <p>If you have questions about these Terms of Service, please contact us:</p>
                    
                    <div class="mt-4 space-y-2">
                        <p><strong class="text-gold">Email:</strong> jackotimespiece@gmail.com</p>
                        <p><strong class="text-gold">Phone:</strong> +91 8160375699</p>
                        <p><strong class="text-gold">Address:</strong> Pune, Maharashtra, India</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="py-20 bg-[#0c0c0c]">
        <div class="container mx-auto px-6 text-center">
            <div class="glass p-12 max-w-3xl mx-auto">
                <h2 class="text-3xl font-serif text-gold mb-6">Questions About Our Terms?</h2>
                <p class="text-gray-300 text-lg mb-8">
                    Our legal team is here to help you understand our terms and conditions.
                </p>
                <a href="<?= CONTACT_PAGE ?>" class="bg-gold text-black px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-gold transition-colors duration-300">
                    Contact Us
                </a>
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
                    targets: '.terms-hero h1, .terms-hero p',
                    opacity: [0, 1],
                    translateY: [30, 0],
                    delay: anime.stagger(200),
                    duration: 1000,
                    easing: 'easeOutCubic'
                });
                
                // Terms sections animation
                anime({
                    targets: '.terms-section',
                    opacity: [0, 1],
                    translateY: [40, 0],
                    delay: anime.stagger(150),
                    duration: 800,
                    easing: 'easeOutCubic'
                });
                
                // Last updated animation
                anime({
                    targets: '.last-updated',
                    opacity: [0, 1],
                    scale: [0.9, 1],
                    duration: 600,
                    easing: 'easeOutBack'
                });
            }
        });
    </script>
</body>
</html> 