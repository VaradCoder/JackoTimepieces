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
    <title>FAQ | JackoTimespiece</title>
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
        .faq-hero {
            background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(201,179,126,0.1) 100%);
            position: relative;
            overflow: hidden;
        }
        .faq-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('/assets/images/brand/faq-bg.jpg') center/cover;
            opacity: 0.3;
            z-index: -1;
        }
        .faq-item {
            background: rgba(24,24,24,0.9);
            border: 1.5px solid #c9b37e;
            border-radius: 1rem;
            margin-bottom: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .faq-item:hover {
            box-shadow: 0 5px 20px rgba(201,179,126,0.2);
        }
        .faq-question {
            padding: 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }
        .faq-question:hover {
            background: rgba(201,179,126,0.1);
        }
        .faq-answer {
            padding: 0 1.5rem;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
            border-top: 0px solid #c9b37e;
        }
        .faq-answer.active {
            padding: 1.5rem;
            max-height: 500px;
            border-top: 1px solid #c9b37e;
        }
        .faq-icon {
            transition: transform 0.3s ease;
        }
        .faq-icon.active {
            transform: rotate(180deg);
        }
        .category-tab {
            background: rgba(24,24,24,0.9);
            border: 1.5px solid #c9b37e;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        .category-tab.active {
            background: #c9b37e;
            color: #000;
        }
        .category-tab:hover {
            background: #c9b37e;
            color: #000;
        }
    </style>
</head>
<body class="bg-black text-white font-sans">
    
    <!-- Hero Section -->
    <section class="faq-hero py-20 relative">
        <div class="container mx-auto px-6 text-center relative z-10">
            <h1 class="text-4xl md:text-6xl font-serif text-gold mb-6">Frequently Asked Questions</h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                Find answers to common questions about our luxury timepieces, shopping experience, and customer service.
            </p>
        </div>
    </section>

    <!-- FAQ Categories -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-serif text-gold mb-4">Browse by Category</h2>
                <p class="text-gray-300">Select a category to find relevant answers</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
                <div class="category-tab active" data-category="shopping">
                    <i class="fas fa-shopping-bag mb-2 block text-xl"></i>
                    <span class="text-sm">Shopping</span>
                </div>
                <div class="category-tab" data-category="shipping">
                    <i class="fas fa-shipping-fast mb-2 block text-xl"></i>
                    <span class="text-sm">Shipping</span>
                </div>
                <div class="category-tab" data-category="warranty">
                    <i class="fas fa-shield-alt mb-2 block text-xl"></i>
                    <span class="text-sm">Warranty</span>
                </div>
                <div class="category-tab" data-category="general">
                    <i class="fas fa-info-circle mb-2 block text-xl"></i>
                    <span class="text-sm">General</span>
                </div>
            </div>
            
            <!-- Shopping FAQ -->
            <div class="faq-category" id="shopping">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">How do I know if a watch is authentic?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">All our watches are sourced directly from authorized dealers and manufacturers. Each timepiece comes with original warranty cards, certificates, and serial numbers. We also provide authenticity verification services and detailed documentation for every purchase.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">Can I try on watches before purchasing?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">Yes! We offer private showroom appointments where you can try on our luxury timepieces. Contact us to schedule a personalized consultation. We also provide virtual try-on services for online customers.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">Do you offer financing options?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">We offer flexible payment options including EMI through our partner banks and credit card companies. Contact our sales team for detailed information about available financing plans.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">What payment methods do you accept?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">We accept all major credit cards (Visa, Mastercard, American Express), debit cards, UPI, net banking, and digital wallets (Google Pay, PhonePe, Paytm). We also offer secure payment gateways for online transactions.</p>
                    </div>
                </div>
            </div>
            
            <!-- Shipping FAQ -->
            <div class="faq-category hidden" id="shipping">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">How long does shipping take?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">Standard shipping takes 3-5 business days within India. Express shipping (1-2 days) is available for select locations. International shipping takes 7-14 business days depending on the destination.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">Is shipping free?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">Yes, we offer free standard shipping on all orders within India. Express shipping and international shipping may incur additional charges which will be clearly displayed during checkout.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">How is my watch packaged for shipping?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">All watches are carefully packaged in luxury boxes with protective padding, original manufacturer packaging, and tamper-evident seals. We use premium courier services with full insurance coverage for safe delivery.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">Do you ship internationally?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">Yes, we ship to most countries worldwide. International shipping includes customs documentation and tracking. Import duties and taxes are the responsibility of the recipient and vary by country.</p>
                    </div>
                </div>
            </div>
            
            <!-- Warranty FAQ -->
            <div class="faq-category hidden" id="warranty">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">What warranty do you provide?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">All watches come with the original manufacturer warranty (typically 2-5 years). We also provide our own 1-year additional warranty covering mechanical issues and servicing. Extended warranty options are available for purchase.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">How do I get my watch serviced?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">We have authorized service centers and partnerships with manufacturer service networks. Contact our customer service team to arrange servicing, and we'll guide you through the process including pickup and delivery.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">What's your return policy?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">We offer a 30-day return policy for unworn watches in original condition with all packaging and documentation. Custom and limited edition pieces may have different return terms. Contact us within 30 days to initiate a return.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">Do you provide maintenance tips?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">Yes, we provide comprehensive care guides with every purchase. Our team also offers personalized maintenance advice and can schedule regular service reminders. We recommend professional servicing every 3-5 years.</p>
                    </div>
                </div>
            </div>
            
            <!-- General FAQ -->
            <div class="faq-category hidden" id="general">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">How can I schedule a showroom appointment?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">Contact us via phone (+91 8160375699) or email (jackotimespiece@gmail.com) to schedule a private consultation. We offer flexible appointment times and personalized service to help you find the perfect timepiece.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">Do you offer trade-ins?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">Yes, we offer trade-in services for luxury watches. Our expert team will evaluate your timepiece and provide a fair market value. Trade-ins can be applied toward the purchase of any watch in our collection.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">Can you help me choose the right watch?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">Absolutely! Our certified watch specialists provide personalized consultations to help you find the perfect timepiece based on your style, budget, and requirements. We consider factors like wrist size, lifestyle, and preferences.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3 class="text-lg font-semibold text-gold">Do you have a loyalty program?</h3>
                        <i class="fas fa-chevron-down faq-icon text-gold"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-300">Yes, our VIP membership program offers exclusive benefits including priority access to new releases, special pricing, complimentary servicing, and exclusive events. Contact us to learn more about membership tiers and benefits.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="py-20 bg-[#0c0c0c]">
        <div class="container mx-auto px-6 text-center">
            <div class="glass p-12 max-w-3xl mx-auto">
                <h2 class="text-3xl font-serif text-gold mb-6">Still Have Questions?</h2>
                <p class="text-gray-300 text-lg mb-8">
                    Our expert team is here to help you with any questions about our luxury timepieces.
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
        // FAQ functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Category tabs
            const categoryTabs = document.querySelectorAll('.category-tab');
            const faqCategories = document.querySelectorAll('.faq-category');
            
            categoryTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const category = this.getAttribute('data-category');
                    
                    // Update active tab
                    categoryTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show/hide categories
                    faqCategories.forEach(cat => {
                        cat.classList.add('hidden');
                        if (cat.id === category) {
                            cat.classList.remove('hidden');
                        }
                    });
                });
            });
            
            // FAQ accordion
            const faqQuestions = document.querySelectorAll('.faq-question');
            
            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    const answer = this.nextElementSibling;
                    const icon = this.querySelector('.faq-icon');
                    
                    // Close other answers
                    faqQuestions.forEach(q => {
                        if (q !== this) {
                            q.nextElementSibling.classList.remove('active');
                            q.querySelector('.faq-icon').classList.remove('active');
                        }
                    });
                    
                    // Toggle current answer
                    answer.classList.toggle('active');
                    icon.classList.toggle('active');
                });
            });
            
            // Animate elements on page load
            if (window.anime) {
                anime({
                    targets: '.faq-hero h1, .faq-hero p',
                    opacity: [0, 1],
                    translateY: [30, 0],
                    delay: anime.stagger(200),
                    duration: 1000,
                    easing: 'easeOutCubic'
                });
                
                anime({
                    targets: '.category-tab',
                    opacity: [0, 1],
                    translateY: [20, 0],
                    delay: anime.stagger(100),
                    duration: 600,
                    easing: 'easeOutCubic'
                });
                
                anime({
                    targets: '.faq-item',
                    opacity: [0, 1],
                    translateY: [20, 0],
                    delay: anime.stagger(50),
                    duration: 600,
                    easing: 'easeOutCubic'
                });
            }
        });
    </script>
</body>
</html> 