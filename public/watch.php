<?php
// --- Handle Add to Cart POST before any output ---
session_start();
require_once __DIR__ . '/../core/config/constants.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['watch_id'])) {
    if (!isset($_SESSION['user'])) {
        header('Location: ' . LOGIN_PAGE);
        exit;
    }
    $watchId = intval($_POST['watch_id']);
    if (!isset($_SESSION['cart'][$watchId])) {
        $_SESSION['cart'][$watchId] = 1;
    } else {
        $_SESSION['cart'][$watchId]++;
    }
    header('Location: ' . CART_PAGE);
    exit;
}

require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../core/db/connection.php';

// --- Fetch product by ID ---
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$watch = null;
if ($id > 0 && isset($conn)) {
  $result = mysqli_query($conn, "SELECT * FROM watches WHERE id = $id LIMIT 1");
  $watch = mysqli_fetch_assoc($result);
}
if (!$watch) {
  echo "<p class='text-center text-white mt-20'>Watch not found.</p>";
  require_once __DIR__ . '/../templates/footer.php';
  exit;
}
?>
<main class="product-main">
  <section class="product-hero">
    <!-- Left: Image -->
    <div class="product-gallery">
      <img src="../assets/images/watches/<?= htmlspecialchars($watch['image']) ?>"
           alt="<?= htmlspecialchars($watch['name']) ?>"
           class="product-img w-full h-[400px] md:h-[500px] object-cover rounded-xl shadow-lg border border-gold"
           onerror="this.onerror=null;this.src='../assets/images/watches/default.jpg';this.classList.add('img-fallback');" />
    </div>
    <!-- Right: Details -->
    <div class="product-info flex flex-col gap-4">
      <h1 class="product-title text-3xl font-serif text-gold mb-2"><?= htmlspecialchars($watch['name']) ?></h1>
      <p class="product-price text-2xl font-bold text-white mb-2">₹<?= number_format($watch['price']) ?></p>
      <p class="product-desc text-gray-300 leading-relaxed mb-4"><?= nl2br(htmlspecialchars($watch['description'])) ?></p>
      <div class="product-actions flex gap-4 mt-6">
        <form method="POST" action="">
          <input type="hidden" name="watch_id" value="<?= $watch['id'] ?>">
          <button type="submit" class="btn-buy px-6 py-2 bg-gold text-black rounded hover:scale-105 transition font-semibold shadow">Add to Cart</button>
        </form>
        <?php if (isset($_SESSION['user'])): ?>
          <button onclick="toggleWishlist(<?= $watch['id'] ?>)" 
                  class="wishlist-btn px-6 py-2 border border-gold text-gold rounded hover:bg-gold hover:text-black transition font-semibold shadow"
                  data-watch-id="<?= $watch['id'] ?>">
            <i class="fas fa-heart mr-2"></i>
            <span class="wishlist-text">Add to Wishlist</span>
          </button>
        <?php else: ?>
          <a href="<?= LOGIN_PAGE ?>" class="px-6 py-2 border border-gold text-gold rounded hover:bg-gold hover:text-black transition font-semibold shadow">
            <i class="fas fa-heart mr-2"></i>
            Login to Wishlist
          </a>
        <?php endif; ?>
        <a href="#model3d" class="btn-try px-6 py-2 border border-gold rounded text-gold hover:bg-gold hover:text-black transition font-semibold shadow">Try Now</a>
      </div>
    </div>
  </section>
  <!-- 3D Model Viewer -->
  <section id="model3d" class="product-3d-viewer bg-[#0c0c0c] py-16 px-6 text-center w-full">
    <h2 class="text-white text-2xl font-serif mb-4">Virtual Preview</h2>
    <div class="max-w-3xl mx-auto bg-[#111] rounded-lg p-4 shadow-xl">
      <?php if (!empty($watch['model_embed_code'])): ?>
        <iframe 
          src="<?= htmlspecialchars($watch['model_embed_code']) ?>" 
          width="100%" height="500" frameborder="0" 
          allowfullscreen 
          class="rounded-md"></iframe>
      <?php elseif (!empty($watch['model_file'])): ?>
        <!-- Example: <model-viewer> for local GLB/GLTF -->
        <model-viewer src="/3d/<?= htmlspecialchars($watch['model_file']) ?>" alt="3D Model" auto-rotate camera-controls style="width:100%;height:500px;background:#111;border-radius:12px;"></model-viewer>
      <?php else: ?>
        <div class="text-gold">3D preview not available for this watch.</div>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
<script src="../assets/js/ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script>
// Entry animation for details
if (window.anime) {
  anime({
    targets: '.product-title, .product-price, .product-desc, .product-actions',
    translateY: [20, 0],
    opacity: [0, 1],
    duration: 600,
    delay: anime.stagger(100),
    easing: 'easeOutQuad'
  });
}
// Smooth scroll for Try Now
const tryBtn = document.querySelector('a.btn-try[href="#model3d"]');
if (tryBtn) {
  tryBtn.addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('model3d').scrollIntoView({ behavior: 'smooth' });
  });
}

// Wishlist functionality
function toggleWishlist(watchId) {
  const button = document.querySelector(`[data-watch-id="${watchId}"]`);
  const icon = button.querySelector('i');
  const text = button.querySelector('.wishlist-text');
  
  // Check if item is in wishlist
  const isInWishlist = icon.classList.contains('text-red-500');
  
  if (isInWishlist) {
    // Remove from wishlist
    fetch('../api/wishlist/remove.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `watch_id=${watchId}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        icon.classList.remove('text-red-500');
        icon.classList.add('text-gold');
        text.textContent = 'Add to Wishlist';
        showNotification(data.message, 'success');
      } else {
        showNotification(data.error || 'Failed to remove from wishlist', 'error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showNotification('Failed to remove from wishlist', 'error');
    });
  } else {
    // Add to wishlist
    fetch('../api/wishlist/add.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `watch_id=${watchId}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        icon.classList.remove('text-gold');
        icon.classList.add('text-red-500');
        text.textContent = 'Remove from Wishlist';
        showNotification(data.message, 'success');
      } else {
        showNotification(data.error || 'Failed to add to wishlist', 'error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showNotification('Failed to add to wishlist', 'error');
    });
  }
}

// Notification function
function showNotification(message, type = 'info') {
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 ${
    type === 'success' ? 'bg-green-600 text-white' : 
    type === 'error' ? 'bg-red-600 text-white' : 
    'bg-gold text-black'
  }`;
  notification.textContent = message;
  
  document.body.appendChild(notification);
  
  // Remove notification after 3 seconds
  setTimeout(() => {
    notification.remove();
  }, 3000);
}

// Check wishlist status on page load
document.addEventListener('DOMContentLoaded', function() {
  <?php if (isset($_SESSION['user'])): ?>
    const watchId = <?= $watch['id'] ?>;
    // Check if this watch is in user's wishlist
    fetch('../api/wishlist/list.php')
    .then(response => response.json())
    .then(data => {
      if (data.success && data.wishlist_items) {
        const isInWishlist = data.wishlist_items.some(item => item.watch_id == watchId);
        if (isInWishlist) {
          const button = document.querySelector(`[data-watch-id="${watchId}"]`);
          if (button) {
            const icon = button.querySelector('i');
            const text = button.querySelector('.wishlist-text');
            icon.classList.remove('text-gold');
            icon.classList.add('text-red-500');
            text.textContent = 'Remove from Wishlist';
          }
        }
      }
    })
    .catch(error => {
      console.error('Error loading wishlist:', error);
    });
  <?php endif; ?>
});
</script> 