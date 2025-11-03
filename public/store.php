<?php
session_start();
require_once __DIR__ . '/../core/config/constants.php';
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../core/db/connection.php';

// --- Build filter/sort SQL ---
$where = [];
$params = [];
$types = '';

// Gender filter
if (!empty($_GET['gender'])) {
    $where[] = 'gender = ?';
    $params[] = $_GET['gender'];
    $types .= 's';
}
// Max price filter
if (!empty($_GET['max_price'])) {
    $where[] = 'price <= ?';
    $params[] = floatval($_GET['max_price']);
    $types .= 'd';
}
// Search filter
if (!empty($_GET['search'])) {
    $where[] = 'name LIKE ?';
    $params[] = '%' . $_GET['search'] . '%';
    $types .= 's';
}

// Sorting
$sort = 'created_at DESC';
if (!empty($_GET['sort'])) {
    if ($_GET['sort'] === 'price_asc') $sort = 'price ASC';
    elseif ($_GET['sort'] === 'price_desc') $sort = 'price DESC';
    elseif ($_GET['sort'] === 'featured') $sort = 'is_featured DESC, created_at DESC';
}

// Build SQL
$sql = 'SELECT * FROM watches';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY ' . $sort;

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Store | JackoTimespiece</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
  <style>
    :root { --gold: #c9b37e; --white: #fff; --black: #000; }
    .text-gold { color: var(--gold) !important; }
    .bg-gold { background: var(--gold) !important; }
    .border-gold { border-color: var(--gold) !important; }
    .font-serif { font-family: 'Playfair Display', serif; }
    .glass { background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); border-radius: 1.5rem; }
    .store-main { min-height: 100vh; }
    .store-filter { min-width: 220px; }
    .store-grid { grid-template-columns: repeat(auto-fit, minmax(270px, 1fr)); }
    .btn-gold { background: var(--gold); color: #000; }
    .btn-gold:hover { background: #fff; color: var(--gold); }
    .input-gold { border: 1.5px solid var(--gold); background: #181818; color: #fff; }
    .input-gold:focus { border-color: #fff; }
    .filter-label { color: var(--gold); font-weight: 600; }
    .active-filter { background: var(--gold); color: #000; }
  </style>
</head>
<body class="bg-black text-white font-sans">
<main class="store-main container mx-auto px-4 py-10 flex flex-col md:flex-row gap-10">
  <!-- Filter Sidebar -->
  <aside class="store-filter glass p-6 mb-8 md:mb-0 md:mr-8 rounded-2xl shadow-lg min-w-[220px] max-w-xs w-full">
    <h2 class="text-xl font-serif text-gold mb-6">Filter</h2>
    <form id="filter-form" method="GET" action="">
      <!-- Gender Filter -->
      <div class="mb-6">
        <label class="filter-label block mb-2">Gender</label>
        <select name="gender" class="input-gold w-full rounded-md px-3 py-2">
          <option value="">All</option>
          <option value="men" <?= isset($_GET['gender']) && $_GET['gender']==='men' ? 'selected' : '' ?>>Men</option>
          <option value="women" <?= isset($_GET['gender']) && $_GET['gender']==='women' ? 'selected' : '' ?>>Women</option>
          <option value="unisex" <?= isset($_GET['gender']) && $_GET['gender']==='unisex' ? 'selected' : '' ?>>Unisex</option>
        </select>
      </div>
      <!-- Price Filter -->
      <div class="mb-6">
        <label class="filter-label block mb-2">Max Price</label>
        <input type="number" name="max_price" min="0" step="100" placeholder="e.g. 10000" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" class="input-gold w-full rounded-md px-3 py-2" />
      </div>
      <!-- Search -->
      <div class="mb-6">
        <label class="filter-label block mb-2">Search</label>
        <input type="text" name="search" placeholder="Search watches..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="input-gold w-full rounded-md px-3 py-2" />
      </div>
      <div class="flex gap-3">
        <button type="submit" class="btn-gold px-5 py-2 rounded-md font-semibold shadow">Apply</button>
        <a href="store.php" class="btn-gold px-5 py-2 rounded-md font-semibold shadow opacity-70">Reset</a>
      </div>
    </form>
  </aside>
  <!-- Main Content -->
  <section class="flex-1 w-full">
    <!-- Page Header & Sort -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
      <div>
        <h1 class="text-3xl md:text-4xl font-serif text-gold mb-2">Our Watches</h1>
        <p class="text-white text-lg opacity-80">Discover luxury timepieces for every style.</p>
      </div>
      <form method="GET" class="flex items-center gap-3">
        <!-- Keep existing filters in sort form -->
        <input type="hidden" name="gender" value="<?= htmlspecialchars($_GET['gender'] ?? '') ?>">
        <input type="hidden" name="max_price" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
        <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <label class="filter-label mr-2">Sort by</label>
        <select name="sort" class="input-gold rounded-md px-3 py-2" onchange="this.form.submit()">
          <option value="newest" <?= (!isset($_GET['sort']) || $_GET['sort']==='newest') ? 'selected' : '' ?>>Newest</option>
          <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort']==='price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
          <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort']==='price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
          <option value="featured" <?= (isset($_GET['sort']) && $_GET['sort']==='featured') ? 'selected' : '' ?>>Featured</option>
        </select>
      </form>
    </div>
    <!-- Product Grid -->
    <div class="store-products">
      <div class="grid grid-cols-2 gap-8">
        <?php
        // Show filtered/sorted watches
        if ($result->num_rows > 0) {
          while ($watch = $result->fetch_assoc()):
        ?>
          <div class="product-card bg-black border-2 border-gold rounded-2xl shadow-lg p-4 lg:p-3 flex flex-col items-center hover:scale-105 transition-transform duration-300 relative group max-w-xs mx-auto">
            <div class="product-card-img-wrap w-full flex justify-center mb-4">
                              <img src="../assets/images/watches/<?= htmlspecialchars($watch['image']) ?>" alt="<?= htmlspecialchars($watch['name']) ?>" class="product-card-img object-cover rounded-xl group-hover:shadow-lg transition-shadow duration-300" />
              <button onclick="toggleWishlist(<?= $watch['id'] ?>)" 
                      class="wishlist-btn absolute top-2 right-2 text-gold bg-black bg-opacity-70 rounded-full p-2 hover:bg-gold hover:text-black transition" 
                      title="Add to Wishlist"
                      data-watch-id="<?= $watch['id'] ?>">
                <i class="fa-solid fa-heart"></i>
              </button>
              <button class="absolute top-2 left-2 text-gold bg-black bg-opacity-70 rounded-full p-2 hover:bg-gold hover:text-black transition" title="Quick View">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
            <h3 class="product-card-title text-lg font-serif text-gold mb-1"><?= htmlspecialchars($watch['name']) ?></h3>
            <p class="product-card-desc text-white text-sm opacity-80 mb-2"><?= htmlspecialchars(mb_strimwidth($watch['description'], 0, 60, '...')) ?></p>
            <span class="product-card-price text-xl font-bold text-gold mb-4">₹<?= number_format($watch['price']) ?></span>
            <a href="watch.php?id=<?= $watch['id'] ?>" class="btn-gold px-6 py-2 rounded-full font-semibold shadow hover:bg-white hover:text-gold transition text-center block">Check Now</a>
          </div>
        <?php
          endwhile;
        } else {
          echo '<div class="col-span-full text-center text-gray-400">No watches found matching your criteria.</div>';
        }
        ?>
      </div>
    </div>
    <!-- Pagination (not implemented) -->
    <div class="flex justify-center mt-12 gap-4">
      <!-- TODO: Replace with real pagination logic -->
      <!-- <a href="#" class="btn-gold px-5 py-2 rounded-md font-semibold shadow opacity-80">&laquo; Prev</a>
      <a href="#" class="btn-gold px-5 py-2 rounded-md font-semibold shadow">Next &raquo;</a> -->
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
<script src="../assets/js/filters.js"></script>
<script src="../assets/js/ui.js"></script>
<script>
// Animate product cards on load
if (window.anime) {
  anime({
    targets: '.product-card',
    opacity: [0,1],
    translateY: [40,0],
    delay: anime.stagger(80),
    duration: 700,
    easing: 'easeOutCubic'
  });
}
// Add fallback styling for missing images
const imgs = document.querySelectorAll('.product-card-img');
imgs.forEach(img => {
  img.addEventListener('error', function() {
    this.src = '../assets/images/watches/default.jpg';
    this.classList.add('img-fallback');
  });
});

// Wishlist functionality
function toggleWishlist(watchId) {
  <?php if (!isset($_SESSION['user'])): ?>
    // Redirect to login if user is not logged in
    window.location.href = '<?= LOGIN_PAGE ?>';
    return;
  <?php endif; ?>

  const button = document.querySelector(`[data-watch-id="${watchId}"]`);
  const icon = button.querySelector('i');
  
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
    // Get user's wishlist items
    fetch('../api/wishlist/list.php')
    .then(response => response.json())
    .then(data => {
      if (data.success && data.wishlist_items) {
        data.wishlist_items.forEach(item => {
          const button = document.querySelector(`[data-watch-id="${item.watch_id}"]`);
          if (button) {
            const icon = button.querySelector('i');
            icon.classList.remove('text-gold');
            icon.classList.add('text-red-500');
          }
        });
      }
    })
    .catch(error => {
      console.error('Error loading wishlist:', error);
    });
  <?php endif; ?>
});
</script>
</body>
</html> 