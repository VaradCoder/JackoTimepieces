<?php
require_once __DIR__ . '/../core/config/constants.php';
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<footer class="bg-[#0c0c0c] border-t border-gold border-opacity-20 mt-20">
  <div class="container mx-auto px-6 py-16">
    <!-- Main Footer Content -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
      
      <!-- Brand Section -->
      <div class="lg:col-span-1">
        <div class="mb-6">
          <h3 class="text-2xl font-serif text-gold mb-4">
            <span class="text-gold">Jacko</span><span class="text-white">Timespiece</span>
          </h3>
          <p class="text-gray-300 text-sm leading-relaxed mb-6">
            Where legacy meets time. Discover the world's most exquisite timepieces, crafted with precision and designed for those who appreciate true luxury.
          </p>
        </div>
        
        <!-- Social Media -->
        <div class="mb-6">
          <h4 class="text-gold font-semibold mb-3 text-sm uppercase tracking-wide">Follow Us</h4>
          <div class="flex space-x-4">
            <a href="#" class="text-gray-400 hover:text-gold transition-colors duration-300">
              <i class="fab fa-facebook-f text-lg"></i>
            </a>
            <a href="<?= INSTA ?>" class="text-gray-400 hover:text-gold transition-colors duration-300">
              <i class="fab fa-instagram text-lg"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-gold transition-colors duration-300">
              <i class="fab fa-twitter text-lg"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-gold transition-colors duration-300">
              <i class="fab fa-youtube text-lg"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-gold transition-colors duration-300">
              <i class="fab fa-linkedin-in text-lg"></i>
            </a>
          </div>
        </div>
        
        <!-- Contact Info -->
        <div>
          <h4 class="text-gold font-semibold mb-3 text-sm uppercase tracking-wide">Contact</h4>
          <div class="space-y-2 text-sm text-gray-300">
            <p><i class="fas fa-phone text-gold mr-2"></i>+91 8160375699</p>
            <p><i class="fas fa-envelope text-gold mr-2"></i>jackotimespiece@gmail.com</p>
            <p><i class="fas fa-map-marker-alt text-gold mr-2"></i>Pune, Maharashtra, India</p>
          </div>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="lg:col-span-1">
        <h4 class="text-gold font-semibold mb-6 text-sm uppercase tracking-wide">Quick Links</h4>
        <ul class="space-y-3">
          <li><a href="<?= STORE_PAGE ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">Shop All Watches</a></li>
          <li><a href="<?= MEN_PAGE ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">Men's Collection</a></li>
          <li><a href="<?= WOMEN_PAGE ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">Women's Collection</a></li>
          <li><a href="<?= UNISEX_PAGE ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">Unisex Collection</a></li>
          <li><a href="<?= ACCOUNT_WISHLIST ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">Wishlist</a></li>
          <li><a href="<?= CART_PAGE ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">Shopping Cart</a></li>
        </ul>
      </div>

      <!-- Customer Service -->
      <div class="lg:col-span-1">
        <h4 class="text-gold font-semibold mb-6 text-sm uppercase tracking-wide">Customer Service</h4>
        <ul class="space-y-3">
          <li><a href="<?= ABOUT_PAGE ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">About Us</a></li>
          <li><a href="<?= CONTACT_PAGE ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">Contact Us</a></li>
          <li><a href="<?= FAQ_PAGE ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">FAQ</a></li>
          <li><a href="<?= RETURN_POLICY_PAGE ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">Return Policy</a></li>
          <li><a href="<?= PRIVACY_PAGE ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">Privacy Policy</a></li>
          <li><a href="<?= TERMS_PAGE ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">Terms of Service</a></li>
        </ul>
      </div>

      <!-- Newsletter & Account -->
      <div class="lg:col-span-1">
        <h4 class="text-gold font-semibold mb-6 text-sm uppercase tracking-wide">Stay Updated</h4>
        
        <!-- Newsletter Signup -->
        <div class="mb-6">
          <p class="text-gray-300 text-sm mb-3">Subscribe to our newsletter for exclusive offers and updates.</p>
          <form class="flex flex-col space-y-3">
            <input type="email" placeholder="Your email address" 
                   class="px-4 py-2 bg-[#181818] border border-gold border-opacity-30 rounded-md text-white text-sm focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">
            <button type="submit" 
                    class="px-4 py-2 bg-gold text-black font-semibold rounded-md text-sm hover:bg-white hover:text-gold transition-colors duration-300">
              Subscribe
            </button>
          </form>
        </div>

        <!-- Account Links -->
        <?php if (isset($_SESSION['user'])): ?>
          <div>
            <h5 class="text-gold font-semibold mb-3 text-sm uppercase tracking-wide">My Account</h5>
            <ul class="space-y-2">
              <li><a href="<?= ACCOUNT_INDEX ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">Dashboard</a></li>
              <li><a href="<?= ACCOUNT_ORDERS ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">Order History</a></li>
              <li><a href="<?= ACCOUNT_SETTINGS ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">Settings</a></li>
              <li><a href="../public/logout.php" class="text-gray-300 hover:text-red-400 transition-colors duration-300 text-sm">Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <div>
            <h5 class="text-gold font-semibold mb-3 text-sm uppercase tracking-wide">Account</h5>
            <ul class="space-y-2">
              <li><a href="<?= LOGIN_PAGE ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">Login</a></li>
              <li><a href="<?= REGISTER_PAGE ?>" class="text-gray-300 hover:text-gold transition-colors duration-300 text-sm">Register</a></li>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Bottom Footer -->
    <div class="border-t border-gold border-opacity-20 pt-8">
      <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
        
        <!-- Copyright -->
        <div class="text-gray-400 text-sm">
          <p>&copy; <?= date('Y') ?> JackoTimespiece. All rights reserved.</p>
          <p class="mt-1"><a href="/Watch/admin/register.php" class="text-gray-500 hover:text-gold transition-colors duration-300 text-xs">Admin</a></p>
        </div>

        <!-- Payment Methods -->
        <div class="flex items-center space-x-4">
          <span class="text-gray-400 text-sm">Secure Payment:</span>
          <div class="flex space-x-2">
            <i class="fab fa-cc-visa text-2xl text-gray-400"></i>
            <i class="fab fa-cc-mastercard text-2xl text-gray-400"></i>
            <i class="fab fa-cc-amex text-2xl text-gray-400"></i>
            <i class="fab fa-cc-paypal text-2xl text-gray-400"></i>
            <i class="fab fa-google-pay text-2xl text-gray-400"></i>
            <i class="fab fa-apple-pay text-2xl text-gray-400"></i>
          </div>
        </div>

        <!-- Trust Badges -->
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-2 text-gray-400 text-sm">
            <i class="fas fa-shield-alt text-gold"></i>
            <span>SSL Secured</span>
          </div>
          <div class="flex items-center space-x-2 text-gray-400 text-sm">
            <i class="fas fa-truck text-gold"></i>
            <span>Free Shipping</span>
          </div>
          <div class="flex items-center space-x-2 text-gray-400 text-sm">
            <i class="fas fa-undo text-gold"></i>
            <span>Easy Returns</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- Back to Top Button -->
<button id="back-to-top" class="fixed bottom-8 right-8 bg-gold text-black p-3 rounded-full shadow-lg hover:bg-white hover:text-gold transition-all duration-300 opacity-0 invisible z-50">
  <i class="fas fa-chevron-up"></i>
</button>

<script>
// Back to Top functionality
document.addEventListener('DOMContentLoaded', function() {
  const backToTopBtn = document.getElementById('back-to-top');
  
  // Show/hide button based on scroll position
  window.addEventListener('scroll', function() {
    if (window.pageYOffset > 300) {
      backToTopBtn.classList.remove('opacity-0', 'invisible');
      backToTopBtn.classList.add('opacity-100', 'visible');
    } else {
      backToTopBtn.classList.add('opacity-0', 'invisible');
      backToTopBtn.classList.remove('opacity-100', 'visible');
    }
  });
  
  // Smooth scroll to top when clicked
  backToTopBtn.addEventListener('click', function() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
  
  // Newsletter form submission
  const newsletterForm = document.querySelector('footer form');
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const email = this.querySelector('input[type="email"]').value;
      if (email) {
        // TODO: Implement newsletter subscription
        alert('Thank you for subscribing to our newsletter!');
        this.reset();
      }
    });
  }
});

// Animate footer elements on scroll
if (window.anime) {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        anime({
          targets: entry.target.querySelectorAll('h4, ul li, .fab, .fas'),
          opacity: [0, 1],
          translateY: [20, 0],
          delay: anime.stagger(50),
          duration: 600,
          easing: 'easeOutCubic'
        });
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  // Observe footer sections
  document.querySelectorAll('footer .grid > div').forEach(section => {
    observer.observe(section);
  });
}
</script> 