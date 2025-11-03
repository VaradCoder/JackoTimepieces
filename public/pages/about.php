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
    <title>About Us | JackoTimespiece</title>
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
        .about-hero {
            background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(201,179,126,0.1) 100%);
            position: relative;
            overflow: hidden;
        }
        .about-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('/assets/images/brand/about-bg.jpg') center/cover;
            opacity: 0.3;
            z-index: -1;
        }
        .timeline-item {
            position: relative;
            padding-left: 3rem;
            margin-bottom: 3rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 2px;
            height: 100%;
            background: linear-gradient(to bottom, #c9b37e, transparent);
        }
        .timeline-dot {
            position: absolute;
            left: -0.5rem;
            top: 0;
            width: 1rem;
            height: 1rem;
            background: #c9b37e;
            border-radius: 50%;
            border: 3px solid #000;
        }
        .value-card {
            background: rgba(24,24,24,0.9);
            border: 1.5px solid #c9b37e;
            border-radius: 1rem;
            padding: 2rem;
            transition: all 0.3s ease;
            text-align: center;
        }
        .value-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(201,179,126,0.2);
        }
        .value-icon {
            width: 80px;
            height: 80px;
            background: #c9b37e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: #000;
            font-size: 2rem;
        }
        .stats-card {
            background: rgba(24,24,24,0.9);
            border: 1.5px solid #c9b37e;
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .stats-card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(201,179,126,0.2);
        }
        .stats-number {
            font-size: 3rem;
            font-weight: bold;
            color: #c9b37e;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body class="bg-black text-white font-sans">
    
    <!-- Hero Section -->
    <section class="about-hero py-20 relative">
        <div class="container mx-auto px-6 text-center relative z-10">
            <h1 class="text-4xl md:text-6xl font-serif text-gold mb-6">Our Story</h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                Where legacy meets time. Discover the journey of JackoTimespiece, from humble beginnings to becoming India's premier destination for luxury timepieces.
            </p>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-serif text-gold mb-6">Our Mission</h2>
                    <p class="text-gray-300 text-lg leading-relaxed mb-6">
                        To curate and offer the world's finest timepieces, providing our discerning clients with not just watches, but heirlooms that tell their unique story. We believe every moment deserves to be marked with precision, elegance, and timeless beauty.
                    </p>
                    <p class="text-gray-300 text-lg leading-relaxed">
                        Our commitment extends beyond selling luxury watches – we're building relationships, creating experiences, and preserving the art of fine watchmaking for generations to come.
                    </p>
                </div>
                <div class="glass p-8">
                    <h3 class="text-2xl font-serif text-gold mb-4">Our Vision</h3>
                    <p class="text-gray-300 leading-relaxed mb-6">
                        To be the most trusted name in luxury timepieces across India, known for our exceptional service, curated collections, and unwavering commitment to quality and authenticity.
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-gold mr-3"></i>
                            <span class="text-gray-300">Authentic luxury timepieces</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-gold mr-3"></i>
                            <span class="text-gray-300">Expert consultation and service</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-gold mr-3"></i>
                            <span class="text-gray-300">Lifetime customer relationships</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Company Timeline -->
    <section class="py-20 bg-[#0c0c0c]">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-serif text-gold mb-4">Our Journey</h2>
                <p class="text-gray-300 text-lg">From passion to excellence - the milestones that shaped JackoTimespiece</p>
            </div>
            
            <div class="max-w-4xl mx-auto">
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="glass p-6">
                        <h3 class="text-xl font-serif text-gold mb-2">2020 - The Beginning</h3>
                        <p class="text-gray-300">Founded in Pune with a vision to bring world-class luxury timepieces to India. Started with a small collection of carefully curated watches.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="glass p-6">
                        <h3 class="text-xl font-serif text-gold mb-2">2021 - First Showroom</h3>
                        <p class="text-gray-300">Opened our first luxury showroom in Pune, offering personalized consultations and exclusive watch experiences.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="glass p-6">
                        <h3 class="text-xl font-serif text-gold mb-2">2022 - Digital Expansion</h3>
                        <p class="text-gray-300">Launched our online platform, making luxury timepieces accessible to customers across India with secure shipping and expert support.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="glass p-6">
                        <h3 class="text-xl font-serif text-gold mb-2">2023 - Growing Trust</h3>
                        <p class="text-gray-300">Established partnerships with leading luxury watch brands and expanded our collection to include rare and limited-edition pieces.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="glass p-6">
                        <h3 class="text-xl font-serif text-gold mb-2">2024 - Excellence</h3>
                        <p class="text-gray-300">Today, JackoTimespiece stands as a symbol of luxury, trust, and excellence in the Indian watch market, serving discerning clients nationwide.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-serif text-gold mb-4">Our Core Values</h2>
                <p class="text-gray-300 text-lg">The principles that guide everything we do</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-gem"></i>
                    </div>
                    <h3 class="text-xl font-serif text-gold mb-3">Excellence</h3>
                    <p class="text-gray-300 text-sm">We pursue perfection in every detail, from our curated collections to our customer service.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="text-xl font-serif text-gold mb-3">Trust</h3>
                    <p class="text-gray-300 text-sm">Building lasting relationships through transparency, authenticity, and unwavering reliability.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="text-xl font-serif text-gold mb-3">Passion</h3>
                    <p class="text-gray-300 text-sm">Our love for fine watchmaking drives us to share the beauty of luxury timepieces with our clients.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="text-xl font-serif text-gold mb-3">Heritage</h3>
                    <p class="text-gray-300 text-sm">Honoring the rich tradition of watchmaking while embracing innovation and modern luxury.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics -->
    <section class="py-20 bg-[#0c0c0c]">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-serif text-gold mb-4">By The Numbers</h2>
                <p class="text-gray-300 text-lg">Our achievements speak for themselves</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="stats-card">
                    <div class="stats-number">500+</div>
                    <p class="text-gray-300">Happy Customers</p>
                </div>
                
                <div class="stats-card">
                    <div class="stats-number">50+</div>
                    <p class="text-gray-300">Luxury Brands</p>
                </div>
                
                <div class="stats-card">
                    <div class="stats-number">1000+</div>
                    <p class="text-gray-300">Timepieces Sold</p>
                </div>
                
                <div class="stats-card">
                    <div class="stats-number">4+</div>
                    <p class="text-gray-300">Years of Excellence</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-serif text-gold mb-4">Meet Our Team</h2>
                <p class="text-gray-300 text-lg">The passionate individuals behind JackoTimespiece</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="glass p-6 text-center">
                    <div class="w-32 h-32 bg-gold rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-black"></i>
                    </div>
                    <h3 class="text-xl font-serif text-gold mb-2">Founder & CEO</h3>
                    <p class="text-gray-300 mb-3">Leading our vision with over 15 years of experience in luxury retail and watchmaking.</p>
                    <p class="text-sm text-gray-400">Expert in luxury timepieces and customer experience</p>
                </div>
                
                <div class="glass p-6 text-center">
                    <div class="w-32 h-32 bg-gold rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-black"></i>
                    </div>
                    <h3 class="text-xl font-serif text-gold mb-2">Head of Sales</h3>
                    <p class="text-gray-300 mb-3">Dedicated to providing exceptional service and finding the perfect watch for every client.</p>
                    <p class="text-sm text-gray-400">Certified watch specialist with international training</p>
                </div>
                
                <div class="glass p-6 text-center">
                    <div class="w-32 h-32 bg-gold rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-black"></i>
                    </div>
                    <h3 class="text-xl font-serif text-gold mb-2">Customer Relations</h3>
                    <p class="text-gray-300 mb-3">Ensuring every customer receives personalized attention and support throughout their journey.</p>
                    <p class="text-sm text-gray-400">Specialist in luxury customer service</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20 bg-[#0c0c0c]">
        <div class="container mx-auto px-6 text-center">
            <div class="glass p-12 max-w-3xl mx-auto">
                <h2 class="text-3xl font-serif text-gold mb-6">Ready to Find Your Perfect Timepiece?</h2>
                <p class="text-gray-300 text-lg mb-8">
                    Join hundreds of satisfied customers who have discovered their perfect watch with JackoTimespiece.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?= STORE_PAGE ?>" class="bg-gold text-black px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-gold transition-colors duration-300">
                        Explore Collection
                    </a>
                    <a href="<?= CONTACT_PAGE ?>" class="border border-gold text-gold px-8 py-3 rounded-full font-semibold hover:bg-gold hover:text-black transition-colors duration-300">
                        Contact Us
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
                    targets: '.about-hero h1, .about-hero p',
                    opacity: [0, 1],
                    translateY: [30, 0],
                    delay: anime.stagger(200),
                    duration: 1000,
                    easing: 'easeOutCubic'
                });
                
                // Timeline animation
                anime({
                    targets: '.timeline-item',
                    opacity: [0, 1],
                    translateX: [-50, 0],
                    delay: anime.stagger(300),
                    duration: 800,
                    easing: 'easeOutCubic'
                });
                
                // Values cards animation
                anime({
                    targets: '.value-card',
                    opacity: [0, 1],
                    translateY: [40, 0],
                    delay: anime.stagger(150),
                    duration: 800,
                    easing: 'easeOutCubic'
                });
                
                // Stats animation
                anime({
                    targets: '.stats-card',
                    opacity: [0, 1],
                    scale: [0.8, 1],
                    delay: anime.stagger(100),
                    duration: 600,
                    easing: 'easeOutBack'
                });
            }
        });
    </script>
</body>
</html> 