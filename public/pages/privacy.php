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
    <title>Privacy Policy | JackoTimespiece</title>
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
        .privacy-hero {
            background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(201,179,126,0.1) 100%);
            position: relative;
            overflow: hidden;
        }
        .privacy-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('/assets/images/brand/privacy-bg.jpg') center/cover;
            opacity: 0.3;
            z-index: -1;
        }
        .policy-section {
            background: rgba(24,24,24,0.9);
            border: 1.5px solid #c9b37e;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        .policy-section:hover {
            box-shadow: 0 5px 20px rgba(201,179,126,0.2);
        }
        .policy-section h3 {
            color: #c9b37e;
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid #c9b37e;
            padding-bottom: 0.5rem;
        }
        .policy-section p, .policy-section li {
            color: #d1d5db;
            line-height: 1.7;
            margin-bottom: 1rem;
        }
        .policy-section ul {
            list-style-type: disc;
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .policy-section ol {
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
    <section class="privacy-hero py-20 relative">
        <div class="container mx-auto px-6 text-center relative z-10">
            <h1 class="text-4xl md:text-6xl font-serif text-gold mb-6">Privacy Policy</h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                Your privacy is important to us. This policy explains how we collect, use, and protect your personal information when you use our services.
            </p>
        </div>
    </section>

    <!-- Privacy Policy Content -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                
                <!-- Last Updated -->
                <div class="last-updated">
                    <p class="text-gold font-semibold">Last Updated: January 15, 2024</p>
                    <p class="text-gray-300 text-sm">This privacy policy is effective as of the date listed above.</p>
                </div>

                <!-- Introduction -->
                <div class="policy-section">
                    <h3>Introduction</h3>
                    <p>
                        JackoTimespiece is committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website, use our services, or interact with us.
                    </p>
                    <p>
                        By using our services, you agree to the collection and use of information in accordance with this policy. If you do not agree with our policies and practices, please do not use our services.
                    </p>
                </div>

                <!-- Information We Collect -->
                <div class="policy-section">
                    <h3>Information We Collect</h3>
                    <p>We collect several types of information from and about users of our website, including:</p>
                    
                    <h4 class="text-gold font-semibold mt-4 mb-2">Personal Information</h4>
                    <ul>
                        <li>Name and contact information (email address, phone number, mailing address)</li>
                        <li>Account credentials and profile information</li>
                        <li>Payment and billing information</li>
                        <li>Purchase history and preferences</li>
                        <li>Communication preferences</li>
                    </ul>

                    <h4 class="text-gold font-semibold mt-4 mb-2">Automatically Collected Information</h4>
                    <ul>
                        <li>Device information (IP address, browser type, operating system)</li>
                        <li>Usage data (pages visited, time spent, links clicked)</li>
                        <li>Cookies and similar tracking technologies</li>
                        <li>Location information (if permitted)</li>
                    </ul>
                </div>

                <!-- How We Use Your Information -->
                <div class="policy-section">
                    <h3>How We Use Your Information</h3>
                    <p>We use the information we collect for various purposes, including:</p>
                    <ul>
                        <li>Providing and maintaining our services</li>
                        <li>Processing and fulfilling your orders</li>
                        <li>Communicating with you about your account and purchases</li>
                        <li>Sending marketing communications (with your consent)</li>
                        <li>Improving our website and services</li>
                        <li>Preventing fraud and ensuring security</li>
                        <li>Complying with legal obligations</li>
                        <li>Providing customer support</li>
                    </ul>
                </div>

                <!-- Information Sharing -->
                <div class="policy-section">
                    <h3>Information Sharing and Disclosure</h3>
                    <p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except in the following circumstances:</p>
                    
                    <h4 class="text-gold font-semibold mt-4 mb-2">Service Providers</h4>
                    <p>We may share your information with trusted third-party service providers who assist us in operating our website, processing payments, delivering orders, and providing customer support.</p>

                    <h4 class="text-gold font-semibold mt-4 mb-2">Legal Requirements</h4>
                    <p>We may disclose your information if required by law, court order, or government regulation, or if we believe such disclosure is necessary to protect our rights, property, or safety.</p>

                    <h4 class="text-gold font-semibold mt-4 mb-2">Business Transfers</h4>
                    <p>In the event of a merger, acquisition, or sale of assets, your information may be transferred as part of the business transaction.</p>
                </div>

                <!-- Data Security -->
                <div class="policy-section">
                    <h3>Data Security</h3>
                    <p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. These measures include:</p>
                    <ul>
                        <li>Encryption of sensitive data during transmission and storage</li>
                        <li>Regular security assessments and updates</li>
                        <li>Access controls and authentication procedures</li>
                        <li>Secure payment processing</li>
                        <li>Employee training on data protection</li>
                    </ul>
                    <p>However, no method of transmission over the internet or electronic storage is 100% secure, and we cannot guarantee absolute security.</p>
                </div>

                <!-- Cookies and Tracking -->
                <div class="policy-section">
                    <h3>Cookies and Tracking Technologies</h3>
                    <p>We use cookies and similar tracking technologies to enhance your browsing experience and analyze website usage. These technologies help us:</p>
                    <ul>
                        <li>Remember your preferences and settings</li>
                        <li>Analyze website traffic and usage patterns</li>
                        <li>Provide personalized content and recommendations</li>
                        <li>Improve website functionality and performance</li>
                    </ul>
                    <p>You can control cookie settings through your browser preferences, though disabling cookies may affect website functionality.</p>
                </div>

                <!-- Your Rights -->
                <div class="policy-section">
                    <h3>Your Rights and Choices</h3>
                    <p>You have certain rights regarding your personal information:</p>
                    
                    <h4 class="text-gold font-semibold mt-4 mb-2">Access and Update</h4>
                    <p>You can access and update your personal information through your account settings or by contacting us directly.</p>

                    <h4 class="text-gold font-semibold mt-4 mb-2">Opt-Out</h4>
                    <p>You can opt out of marketing communications by following the unsubscribe instructions in our emails or contacting us.</p>

                    <h4 class="text-gold font-semibold mt-4 mb-2">Data Deletion</h4>
                    <p>You can request deletion of your personal information, subject to legal and contractual obligations.</p>

                    <h4 class="text-gold font-semibold mt-4 mb-2">Data Portability</h4>
                    <p>You can request a copy of your personal information in a portable format.</p>
                </div>

                <!-- Children's Privacy -->
                <div class="policy-section">
                    <h3>Children's Privacy</h3>
                    <p>Our services are not intended for children under the age of 18. We do not knowingly collect personal information from children under 18. If you believe we have collected information from a child under 18, please contact us immediately.</p>
                </div>

                <!-- International Transfers -->
                <div class="policy-section">
                    <h3>International Data Transfers</h3>
                    <p>Your information may be transferred to and processed in countries other than your own. We ensure that such transfers comply with applicable data protection laws and implement appropriate safeguards to protect your information.</p>
                </div>

                <!-- Policy Updates -->
                <div class="policy-section">
                    <h3>Changes to This Privacy Policy</h3>
                    <p>We may update this Privacy Policy from time to time to reflect changes in our practices or applicable laws. We will notify you of any material changes by:</p>
                    <ul>
                        <li>Posting the updated policy on our website</li>
                        <li>Sending you an email notification</li>
                        <li>Displaying a notice on our website</li>
                    </ul>
                    <p>Your continued use of our services after such changes constitutes acceptance of the updated policy.</p>
                </div>

                <!-- Contact Information -->
                <div class="policy-section">
                    <h3>Contact Us</h3>
                    <p>If you have any questions about this Privacy Policy or our data practices, please contact us:</p>
                    
                    <div class="mt-4 space-y-2">
                        <p><strong class="text-gold">Email:</strong> jackotimespiece@gmail.com</p>
                        <p><strong class="text-gold">Phone:</strong> +91 8160375699</p>
                        <p><strong class="text-gold">Address:</strong> Pune, Maharashtra, India</p>
                    </div>
                    
                    <p class="mt-4">
                        We will respond to your inquiry within 30 days of receipt.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="py-20 bg-[#0c0c0c]">
        <div class="container mx-auto px-6 text-center">
            <div class="glass p-12 max-w-3xl mx-auto">
                <h2 class="text-3xl font-serif text-gold mb-6">Questions About Privacy?</h2>
                <p class="text-gray-300 text-lg mb-8">
                    Our team is here to help you understand how we protect your information.
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
                    targets: '.privacy-hero h1, .privacy-hero p',
                    opacity: [0, 1],
                    translateY: [30, 0],
                    delay: anime.stagger(200),
                    duration: 1000,
                    easing: 'easeOutCubic'
                });
                
                // Policy sections animation
                anime({
                    targets: '.policy-section',
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