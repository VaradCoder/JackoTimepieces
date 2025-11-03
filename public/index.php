<?php 
session_start(); 
require_once __DIR__ . '/../core/db/connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JackoTimespiece — Where Legacy Meets Time</title>
  <!-- TailwindCSS & FontAwesome -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="../assets/css/style.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <style>
    :root { --gold: #c9b37e; --white: #fff; --black: #000; }
    .text-gold { color: var(--gold) !important; }
    .bg-gold { background: var(--gold) !important; }
    .border-gold { border-color: var(--gold) !important; }
    .font-serif { font-family: 'Playfair Display', serif; }
    .glass { background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); border-radius: 1.5rem; }
    .badge-gold { background: var(--gold); color: var(--black); font-weight: bold; border-radius: 9999px; padding: 0.25em 0.75em; font-size: 0.85em; }
    .shine { position: relative; overflow: hidden; }
    .shine::after { content: ''; position: absolute; top: 0; left: -75%; width: 50%; height: 100%; background: linear-gradient(120deg,rgba(255,255,255,0) 0%,rgba(255,255,255,0.4) 50%,rgba(255,255,255,0) 100%); transform: skewX(-20deg); animation: shine 2.5s infinite; }
    @keyframes shine { 0%{left:-75%;} 60%{left:120%;} 100%{left:120%;} }
    body, .bg-black { background: #000 !important; }
    .text-white { color: #fff !important; }
    .border-white { border-color: #fff !important; }
    .hover\:text-gold:hover { color: var(--gold) !important; }
    .hover\:bg-gold:hover { background: var(--gold) !important; color: #000 !important; }
    .hover\:border-gold:hover { border-color: var(--gold) !important; }
    .shadow-lg, .shadow-md, .shadow-2xl { box-shadow: 0 4px 24px 0 rgba(0,0,0,0.5) !important; }
  </style>
</head>
<body class="bg-black text-white font-sans">
  <?php include __DIR__ . '/../templates/header.php'; ?>

  <!-- Hero Section -->
  <section class="relative min-h-screen flex flex-col justify-center items-center overflow-hidden">
    <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover opacity-30 z-0">
              <source src="../assets/images/banners/hero.mp4" type="video/mp4">
    </video>
    <div class="relative z-10 text-center px-6 py-24">
      <h1 id="hero-headline" class="text-5xl md:text-7xl font-serif text-gold mb-6 shine">Where Legacy Meets Time</h1>
      <p class="text-xl md:text-2xl text-gold mb-8">Discover the world’s most exquisite timepieces</p>
      <a href="/store.php" class="inline-block px-8 py-4 bg-gold text-black font-semibold rounded-full shadow-lg hover:scale-105 transition-transform duration-300">Shop Now</a>
    </div>
  </section>

  <!-- Featured Watches Carousel -->
  <section class="py-20 bg-black">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl md:text-4xl font-serif text-gold mb-10 text-center tracking-wide">Featured Watches</h2>
      <div class="swiper featured-swiper">
        <div class="swiper-wrapper">
          <?php
          $featured_query = "SELECT * FROM watches WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 6";
          $featured_result = $conn->query($featured_query);
          if ($featured_result && $featured_result->num_rows > 0):
            while ($watch = $featured_result->fetch_assoc()): ?>
              <div class="swiper-slide">
                <div class="product-card glass bg-black/80 border-2 border-gold rounded-2xl shadow-lg p-6 flex flex-col items-center hover:scale-105 transition-transform duration-300 relative max-w-xs mx-auto">
                  <?php if (!empty($watch['is_limited'])): ?><span class="badge absolute top-3 left-3 bg-gold text-black text-xs px-3 py-1 rounded-full font-bold">Limited</span>
                  <img src="../assets/images/watches/<?= htmlspecialchars($watch['image']) ?>" alt="<?= htmlspecialchars($watch['name']) ?>" class="product-card-img w-40 h-40 object-cover rounded-xl mb-4" onerror="this.onerror=null;this.src='../assets/images/watches/default.jpg';this.classList.add('img-fallback');" /><?php endif; ?>
                  <h3 class="text-xl font-serif text-gold mb-2 text-center"><?= htmlspecialchars($watch['name']) ?></h3>
                  <p class="text-gold mb-2 text-center opacity-80"><?= htmlspecialchars(mb_strimwidth($watch['description'], 0, 50, '...')) ?></p>
                  <span class="text-lg font-bold text-white mb-4">₹<?= number_format($watch['price']) ?></span>
                  <a href="../public/watch.php?id=<?= $watch['id'] ?>" class="px-6 py-2 bg-gold text-black rounded-full font-semibold hover:bg-white hover:text-gold transition shadow">View Details</a>
                </div>
              </div>
            <?php endwhile;
          else:
            for ($i = 1; $i <= 3; $i++): ?>
              <div class="swiper-slide">
                <div class="product-card glass bg-black/80 border-2 border-gold rounded-2xl shadow-lg p-6 flex flex-col items-center hover:scale-105 transition-transform duration-300 relative max-w-xs mx-auto">
                  <img src="../assets/images/watches/<?= $i ?>.jpg" alt="Watch <?= $i ?>" class="product-card-img w-40 h-40 object-cover rounded-xl mb-4" onerror="this.onerror=null;this.src='../assets/images/watches/default.jpg';this.classList.add('img-fallback');" />
                  <h3 class="text-xl font-serif text-gold mb-2 text-center">Watch Model <?= $i ?></h3>
                  <p class="text-gold mb-2 text-center opacity-80">Luxury description goes here.</p>
                  <span class="text-lg font-bold text-white mb-4">₹<?= 5000 + $i * 100 ?></span>
                  <a href="/public/watch.php?id=<?= $i ?>" class="px-6 py-2 bg-gold text-black rounded-full font-semibold hover:bg-white hover:text-gold transition shadow">View Details</a>
                </div>
              </div>
            <?php endfor;
          endif; ?>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
      </div>
    </div>
  </section>

  <!-- Limited Editions & New Arrivals -->
  <section class="py-16 bg-black">
    <div class="container mx-auto px-4">
      <div class="grid md:grid-cols-2 gap-12">
        <!-- Limited Editions -->
        <div class="glass p-8 flex flex-col items-center border-2 border-gold rounded-2xl shadow-lg">
          <h3 class="text-2xl font-serif text-gold mb-4 tracking-wide">Limited Editions</h3>
          <div class="flex flex-col gap-6 w-full">
            <?php
            $limited_query = "SELECT * FROM watches WHERE is_limited = 1 ORDER BY created_at DESC LIMIT 2";
            $limited_result = $conn->query($limited_query);
            if ($limited_result && $limited_result->num_rows > 0):
              while ($watch = $limited_result->fetch_assoc()): ?>
                <div class="flex items-center border-2 border-gold rounded-xl p-3 bg-black/80 glass relative group hover:shadow-xl transition-shadow duration-300">
                  <span class="badge absolute top-3 left-3 bg-gold text-black text-xs px-3 py-1 rounded-full font-bold">Limited</span>
                  <div class="mr-4">
                    <img src="../assets/images/watches/<?= htmlspecialchars($watch['image']) ?>" alt="<?= htmlspecialchars($watch['name']) ?>" class="w-16 h-16 object-cover rounded-lg shadow group-hover:scale-105 transition-transform duration-200" onerror="this.onerror=null;this.src='../assets/images/watches/default.jpg';this.classList.add('img-fallback');" />
                  </div>
                  <div class="flex-1 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                      <span class="font-serif text-lg text-gold"><?= htmlspecialchars($watch['name']) ?></span>
                      <form method="POST" action="../public/watch.php" style="display: inline;">
                        <input type="hidden" name="watch_id" value="<?= $watch['id'] ?>">
                        <button type="submit" class="px-3 py-1 text-xs bg-gold text-black rounded-full font-semibold shadow hover:scale-105 transition-transform duration-200 ml-2">Add to Cart</button>
                      </form>
                    </div>
                    <span class="text-gold text-xs mt-1">₹<?= number_format($watch['price']) ?></span>
                  </div>
                </div>
              <?php endwhile;
            else:
              for ($i = 1; $i <= 2; $i++): ?>
                <div class="flex items-center border-2 border-gold rounded-xl p-3 bg-black/80 glass relative group hover:shadow-xl transition-shadow duration-300">
                  <span class="badge absolute top-3 left-3 bg-gold text-black text-xs px-3 py-1 rounded-full font-bold">Limited</span>
                  <div class="mr-4">
                    <img src="../assets/images/watches/<?= $i ?>.jpg" alt="Limited <?= $i ?>" class="w-16 h-16 object-cover rounded-lg shadow group-hover:scale-105 transition-transform duration-200" onerror="this.onerror=null;this.src='../assets/images/watches/default.jpg';this.classList.add('img-fallback');" />
                  </div>
                  <div class="flex-1 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                      <span class="font-serif text-lg text-gold">Model L<?= $i ?></span>
                      <button class="px-3 py-1 text-xs bg-gold text-black rounded-full font-semibold shadow hover:scale-105 transition-transform duration-200 ml-2">Add to Cart</button>
                    </div>
                    <span class="text-gold text-xs mt-1">Only <?= 50 - $i*10 ?> left</span>
                  </div>
                </div>
              <?php endfor;
            endif; ?>
          </div>
        </div>
        <!-- New Arrivals -->
        <div class="glass p-8 flex flex-col items-center border-2 border-gold rounded-2xl shadow-lg">
          <h3 class="text-2xl font-serif text-gold mb-4 tracking-wide">New Arrivals</h3>
          <div class="flex flex-col gap-6 w-full">
            <?php
            $new_arrivals_query = "SELECT * FROM watches ORDER BY created_at DESC LIMIT 2";
            $new_arrivals_result = $conn->query($new_arrivals_query);
            if ($new_arrivals_result && $new_arrivals_result->num_rows > 0):
              while ($watch = $new_arrivals_result->fetch_assoc()): ?>
                <div class="flex items-center border-2 border-gold rounded-xl p-3 bg-black/80 glass relative group hover:shadow-xl transition-shadow duration-300">
                  <span class="badge absolute top-3 left-3 bg-gold text-black text-xs px-3 py-1 rounded-full font-bold">New</span>
                  <div class="mr-4">
                    <img src="../assets/images/watches/<?= htmlspecialchars($watch['image']) ?>" alt="<?= htmlspecialchars($watch['name']) ?>" class="w-16 h-16 object-cover rounded-lg shadow group-hover:scale-105 transition-transform duration-200" onerror="this.onerror=null;this.src='../assets/images/watches/default.jpg';this.classList.add('img-fallback');" /> 
                  </div>
                  <div class="flex-1 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                      <span class="font-serif text-lg text-gold"><?= htmlspecialchars($watch['name']) ?></span>
                      <form method="POST" action="../public/watch.php" style="display: inline;">
                        <input type="hidden" name="watch_id" value="<?= $watch['id'] ?>">
                        <button type="submit" class="px-3 py-1 text-xs bg-gold text-black rounded-full font-semibold shadow hover:scale-105 transition-transform duration-200 ml-2">Add to Cart</button>
                      </form>
                    </div>
                    <span class="text-gold text-xs mt-1">₹<?= number_format($watch['price']) ?></span>
                  </div>
                </div>
              <?php endwhile;
            else:
              for ($i = 1; $i <= 2; $i++): ?>
                <div class="flex items-center border-2 border-gold rounded-xl p-3 bg-black/80 glass relative group hover:shadow-xl transition-shadow duration-300">
                  <span class="badge absolute top-3 left-3 bg-gold text-black text-xs px-3 py-1 rounded-full font-bold">New</span>
                  <div class="mr-4">
                    <img src="/assets/images/watches/new<?= $i ?>.jpg" alt="New <?= $i ?>" class="w-16 h-16 object-cover rounded-lg shadow group-hover:scale-105 transition-transform duration-200" onerror="this.onerror=null;this.src='../assets/images/watches/default.jpg';this.classList.add('img-fallback');" />
                  </div>
                  <div class="flex-1 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                      <span class="font-serif text-lg text-gold">Model N<?= $i ?></span>
                      <button class="px-3 py-1 text-xs bg-gold text-black rounded-full font-semibold shadow hover:scale-105 transition-transform duration-200 ml-2">Add to Cart</button>
                    </div>
                    <span class="text-gold text-xs mt-1">Just arrived</span>
                  </div>
                </div>
              <?php endfor;
            endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Brand Philosophy / Storytelling -->
  <section class="py-20 bg-black">
    <div class="container mx-auto px-4 flex flex-col md:flex-row items-center gap-12">
      <div class="flex-1">
        <h2 class="text-4xl font-serif text-white mb-6">A Legacy of Excellence</h2>
        <p class="text-lg text-gold mb-6">At JackoTimespiece, we believe a watch is more than a timekeeper—it's a statement of legacy, craftsmanship, and style. Our artisans blend tradition with innovation, creating heirlooms for generations.</p>
        <blockquote class="font-serif text-xl text-gold border-l-4 border-gold pl-4 mb-4">“Time is the ultimate luxury.”</blockquote>
        <div class="text-white">— The Founder</div>
      </div>
      <div class="flex-1 flex justify-center">
        <img src="../assets/images/brand/story.jpg" alt="Brand Story" class="rounded-3xl shadow-2xl w-full max-w-md object-cover">
      </div>
    </div>
  </section>

  <!-- Testimonials / Press Mentions -->
  <section class="py-20 bg-black">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-serif text-gold mb-10 text-center">What Our Clients Say</h2>
      <div class="swiper testimonials-swiper">
        <div class="swiper-wrapper">
          <div class="swiper-slide">
            <div class="bg-black rounded-2xl p-8 shadow-lg text-center">
              <i class="fa-solid fa-star text-gold text-2xl mb-2"></i>
              <blockquote class="font-serif text-xl text-gold mb-4">“The craftsmanship is unmatched. My Jacko watch is a true heirloom.”</blockquote>
              <span class="text-gold font-bold">— Rohan S.</span>
            </div>
          </div>
          <div class="swiper-slide">
            <div class="bg-black rounded-2xl p-8 shadow-lg text-center">
              <i class="fa-solid fa-star text-gold text-2xl mb-2"></i>
              <blockquote class="font-serif text-xl text-gold mb-4">“Luxury, precision, and style. I get compliments every day.”</blockquote>
              <span class="text-gold font-bold">— Tejas D.</span>
            </div>
          </div>
          <div class="swiper-slide">
            <div class="bg-black rounded-2xl p-8 shadow-lg text-center">
              <i class="fa-solid fa-star text-gold text-2xl mb-2"></i>
              <blockquote class="font-serif text-xl text-gold mb-4">“A seamless shopping experience and a stunning timepiece.”</blockquote>
              <span class="text-gold font-bold">— Varad B.</span>
            </div>
          </div>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
  </section>

  <!-- Newsletter Subscription CTA -->
  <section class="py-16 bg-black">
    <div class="container mx-auto px-4">
      <div class="max-w-xl mx-auto glass p-10 shadow-lg flex flex-col items-center">
        <h2 class="text-2xl font-serif text-gold mb-4">Join Our Newsletter</h2>
        <form id="newsletter-form" class="w-full flex flex-col md:flex-row gap-4" method="POST" action="/api/newsletter/subscribe.php">
          <input type="email" name="email" required placeholder="Your email address" class="flex-1 px-6 py-3 rounded-full bg-black text-white border border-gold focus:outline-none focus:ring-2 focus:ring-gold" />
          <button type="submit" class="px-8 py-3 bg-gold text-black font-semibold rounded-full shadow-lg hover:scale-105 transition-transform duration-300">Subscribe</button>
        </form>
        <div id="newsletter-message" class="mt-4 text-center"></div>
      </div>
    </div>
  </section>

  <?php include __DIR__ . '/../templates/footer.php'; ?>

  <script>
    // Hero headline animation
    document.addEventListener('DOMContentLoaded', function() {
      if (window.anime) {
        anime({
          targets: '#hero-headline',
          opacity: [0,1],
          translateY: [40,0],
          easing: 'easeOutExpo',
          duration: 1200
        });
      }
      // Swiper for featured watches
      if (window.Swiper) {
        new Swiper('.featured-swiper', {
          slidesPerView: 1,
          spaceBetween: 30,
          loop: true,
          pagination: { el: '.swiper-pagination', clickable: true },
          navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
          breakpoints: {
            640: { slidesPerView: 2 },
            1024: { slidesPerView: 3 }
          }
        });
        new Swiper('.testimonials-swiper', {
          slidesPerView: 1,
          loop: true,
          pagination: { el: '.swiper-pagination', clickable: true },
          autoplay: { delay: 5000 }
        });
      }
      // Newsletter AJAX
      const form = document.getElementById('newsletter-form');
      if (form) {
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          const email = form.email.value;
          const msg = document.getElementById('newsletter-message');
          msg.textContent = '';
          fetch('/api/newsletter/subscribe.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'email=' + encodeURIComponent(email)
          })
          .then(res => res.json())
          .then(data => {
            msg.textContent = data.success ? 'Thank you for subscribing!' : (data.error || 'Subscription failed.');
            msg.className = data.success ? 'text-gold' : 'text-red-500';
            if (data.success) form.reset();
          })
          .catch(() => {
            msg.textContent = 'An error occurred.';
            msg.className = 'text-red-500';
          });
        });
      }
    });
  </script>
  <style>
    .glow-gold {
      box-shadow: 0 0 8px 2px #c9b37e, 0 0 2px 1px #fff2cc;
      animation: glowGold 1.5s infinite alternate;
    }
    @keyframes glowGold {
      from { box-shadow: 0 0 8px 2px #c9b37e, 0 0 2px 1px #fff2cc; }
      to   { box-shadow: 0 0 16px 4px #c9b37e, 0 0 4px 2px #fffbe6; }
    }
  </style>
  <script src="../assets/js/ui.js"></script>
</body>
</html> 