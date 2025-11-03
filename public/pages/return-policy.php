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
    <title>Return Policy | JackoTimespiece</title>
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
        .return-hero {
            background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(201,179,126,0.1) 100%);
            position: relative;
            overflow: hidden;
        }
        .return-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('/assets/images/brand/return-bg.jpg') center/cover;
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
        .step-card {
            background: rgba(24,24,24,0.9);
            border: 1.5px solid #c9b37e;
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .step-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(201,179,126,0.2);
        }
        .step-number {
            width: 50px;
            height: 50px;
            background: #c9b37e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: #000;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .exceptions-box {
            background: rgba(239,68,68,0.1);
            border: 1px solid #ef4444;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .exceptions-box h4 {
            color: #ef4444;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body class="bg-black text-white font-sans">
    
    <!-- Hero Section -->
    <section class="return-hero py-20 relative">
        <div class="container mx-auto px-6 text-center relative z-10">
            <h1 class="text-4xl md:text-6xl font-serif text-gold mb-6">Return Policy</h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                We want you to be completely satisfied with your purchase. Our return policy ensures a hassle-free experience for our valued customers.
            </p>
        </div>
    </section>

    <!-- Return Policy Content -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                
                <!-- Overview -->
                <div class="policy-section">
                    <h3>Return Policy Overview</h3>
                    <p>
                        At JackoTimespiece, we stand behind the quality of our luxury timepieces. We offer a comprehensive 30-day return policy to ensure your complete satisfaction with your purchase.
                    </p>
                    <p>
                        Our return policy is designed to be fair, transparent, and customer-friendly while maintaining the integrity of our luxury products.
                    </p>
                </div>

                <!-- Return Conditions -->
                <div class="policy-section">
                    <h3>Return Conditions</h3>
                    <p>To be eligible for a return, your item must meet the following criteria:</p>
                    
                    <h4 class="text-gold font-semibold mt-4 mb-2">General Requirements</h4>
                    <ul>
                        <li>Return request must be initiated within 30 days of delivery</li>
                        <li>Item must be unworn and in original condition</li>
                        <li>All original packaging and documentation must be included</li>
                        <li>Serial numbers and authenticity cards must be intact</li>
                        <li>No signs of wear, damage, or modification</li>
                        <li>Original price tags and protective films must be present</li>
                    </ul>

                    <h4 class="text-gold font-semibold mt-4 mb-2">Required Documentation</h4>
                    <ul>
                        <li>Original invoice or receipt</li>
                        <li>Warranty card (if applicable)</li>
                        <li>Certificate of authenticity (if applicable)</li>
                        <li>All instruction manuals and booklets</li>
                    </ul>
                </div>

                <!-- Return Process -->
                <div class="policy-section">
                    <h3>Return Process</h3>
                    <p>Follow these simple steps to return your item:</p>
                    
                    <div class="grid md:grid-cols-3 gap-6 mt-6">
                        <div class="step-card">
                            <div class="step-number">1</div>
                            <h4 class="text-gold font-semibold mb-2">Contact Us</h4>
                            <p class="text-sm text-gray-300">Call us at +91 8160375699 or email jackotimespiece@gmail.com to initiate your return request.</p>
                        </div>
                        
                        <div class="step-card">
                            <div class="step-number">2</div>
                            <h4 class="text-gold font-semibold mb-2">Get Authorization</h4>
                            <p class="text-sm text-gray-300">We'll provide you with a return authorization number and shipping instructions.</p>
                        </div>
                        
                        <div class="step-card">
                            <div class="step-number">3</div>
                            <h4 class="text-gold font-semibold mb-2">Ship Back</h4>
                            <p class="text-sm text-gray-300">Package your item securely and ship it back using the provided shipping label.</p>
                        </div>
                    </div>
                </div>

                <!-- Non-Returnable Items -->
                <div class="policy-section">
                    <h3>Non-Returnable Items</h3>
                    <p>The following items are not eligible for return:</p>
                    
                    <div class="exceptions-box">
                        <h4>Custom and Personalized Items</h4>
                        <ul>
                            <li>Watches with custom engravings or modifications</li>
                            <li>Personalized straps or accessories</li>
                            <li>Made-to-order timepieces</li>
                        </ul>
                    </div>
                    
                    <div class="exceptions-box">
                        <h4>Limited Edition and Rare Items</h4>
                        <ul>
                            <li>Limited edition watches with numbered certificates</li>
                            <li>Vintage or rare timepieces</li>
                            <li>Items marked as "Final Sale"</li>
                        </ul>
                    </div>
                    
                    <div class="exceptions-box">
                        <h4>Used or Damaged Items</h4>
                        <ul>
                            <li>Items showing signs of wear or use</li>
                            <li>Damaged or modified products</li>
                            <li>Items missing original packaging or documentation</li>
                        </ul>
                    </div>
                </div>

                <!-- Refund Information -->
                <div class="policy-section">
                    <h3>Refund Information</h3>
                    
                    <h4 class="text-gold font-semibold mt-4 mb-2">Refund Processing</h4>
                    <ul>
                        <li>Refunds are processed within 14 business days of receiving the returned item</li>
                        <li>Refunds are issued to the original payment method</li>
                        <li>Processing times may vary depending on your bank or payment provider</li>
                        <li>You will receive an email confirmation when your refund is processed</li>
                    </ul>

                    <h4 class="text-gold font-semibold mt-4 mb-2">Deductions</h4>
                    <p>The following amounts may be deducted from your refund:</p>
                    <ul>
                        <li>Return shipping costs (if applicable)</li>
                        <li>Restocking fees for high-value items (5% of item value)</li>
                        <li>Any damage or missing components</li>
                    </ul>
                </div>

                <!-- Shipping Information -->
                <div class="policy-section">
                    <h3>Shipping Information</h3>
                    
                    <h4 class="text-gold font-semibold mt-4 mb-2">Return Shipping</h4>
                    <ul>
                        <li>We provide prepaid shipping labels for returns within India</li>
                        <li>International returns may incur shipping costs</li>
                        <li>Items must be shipped using our authorized carriers</li>
                        <li>Tracking information is required for all returns</li>
                    </ul>

                    <h4 class="text-gold font-semibold mt-4 mb-2">Packaging Requirements</h4>
                    <ul>
                        <li>Use the original packaging when possible</li>
                        <li>Ensure adequate protection during transit</li>
                        <li>Include all original documentation</li>
                        <li>Attach the return authorization label clearly</li>
                    </ul>
                </div>

                <!-- Warranty Information -->
                <div class="policy-section">
                    <h3>Warranty Information</h3>
                    
                    <h4 class="text-gold font-semibold mt-4 mb-2">Manufacturer Warranty</h4>
                    <ul>
                        <li>All watches come with original manufacturer warranty</li>
                        <li>Warranty periods vary by brand (typically 2-5 years)</li>
                        <li>Warranty covers manufacturing defects only</li>
                        <li>Normal wear and tear are not covered</li>
                    </ul>

                    <h4 class="text-gold font-semibold mt-4 mb-2">Extended Warranty</h4>
                    <ul>
                        <li>We offer extended warranty options for purchase</li>
                        <li>Extended warranties provide additional coverage</li>
                        <li>Coverage includes mechanical issues and servicing</li>
                        <li>Terms and conditions apply to extended warranties</li>
                    </ul>
                </div>

                <!-- Exchange Policy -->
                <div class="policy-section">
                    <h3>Exchange Policy</h3>
                    <p>We offer exchanges for items of equal or greater value:</p>
                    
                    <h4 class="text-gold font-semibold mt-4 mb-2">Exchange Conditions</h4>
                    <ul>
                        <li>Same return conditions apply</li>
                        <li>Exchanges must be for items currently in stock</li>
                        <li>Price differences will be adjusted accordingly</li>
                        <li>Exchange processing time: 7-10 business days</li>
                    </ul>

                    <h4 class="text-gold font-semibold mt-4 mb-2">Exchange Process</h4>
                    <ol>
                        <li>Contact us to discuss exchange options</li>
                        <li>Return the original item following return procedures</li>
                        <li>Select your new item from available inventory</li>
                        <li>Pay any price difference if applicable</li>
                        <li>Receive your new item once return is processed</li>
                    </ol>
                </div>

                <!-- Contact Information -->
                <div class="policy-section">
                    <h3>Need Help with Returns?</h3>
                    <p>Our customer service team is here to help you with any questions about returns, exchanges, or refunds.</p>
                    
                    <div class="mt-6 space-y-4">
                        <div class="flex items-center">
                            <i class="fas fa-phone text-gold mr-3"></i>
                            <div>
                                <p class="text-gold font-semibold">Phone Support</p>
                                <p class="text-gray-300">+91 8160375699</p>
                                <p class="text-sm text-gray-400">Monday - Friday: 12:20 AM - 8:00 PM IST</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-gold mr-3"></i>
                            <div>
                                <p class="text-gold font-semibold">Email Support</p>
                                <p class="text-gray-300">jackotimespiece@gmail.com</p>
                                <p class="text-sm text-gray-400">Response within 24 hours</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt text-gold mr-3"></i>
                            <div>
                                <p class="text-gold font-semibold">Showroom Returns</p>
                                <p class="text-gray-300">Pune, Maharashtra, India</p>
                                <p class="text-sm text-gray-400">By appointment only</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="py-20 bg-[#0c0c0c]">
        <div class="container mx-auto px-6 text-center">
            <div class="glass p-12 max-w-3xl mx-auto">
                <h2 class="text-3xl font-serif text-gold mb-6">Questions About Returns?</h2>
                <p class="text-gray-300 text-lg mb-8">
                    Our customer service team is ready to help you with any return-related questions.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?= CONTACT_PAGE ?>" class="bg-gold text-black px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-gold transition-colors duration-300">
                        Contact Us
                    </a>
                    <a href="tel:+918160375699" class="border border-gold text-gold px-8 py-3 rounded-full font-semibold hover:bg-gold hover:text-black transition-colors duration-300">
                        Call Now
                    </a>
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
                    targets: '.return-hero h1, .return-hero p',
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
                
                // Step cards animation
                anime({
                    targets: '.step-card',
                    opacity: [0, 1],
                    translateY: [30, 0],
                    delay: anime.stagger(100),
                    duration: 600,
                    easing: 'easeOutCubic'
                });
            }
        });
    </script>
</body>
</html> 