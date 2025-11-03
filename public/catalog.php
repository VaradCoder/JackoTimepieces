<?php
session_start();
require_once __DIR__ . '/../core/config/constants.php';
require_once __DIR__ . '/../core/db/connection.php';

// Get filter parameters
$category = $_GET['category'] ?? '';
$price_min = $_GET['price_min'] ?? '';
$price_max = $_GET['price_max'] ?? '';
$sort = $_GET['sort'] ?? 'name';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT w.*, c.name as category_name FROM watches w 
          LEFT JOIN categories c ON w.category_id = c.id 
          WHERE w.status = 'active'";

$params = [];
$types = '';

if ($category) {
    $query .= " AND c.slug = ?";
    $params[] = $category;
    $types .= 's';
}

if ($price_min !== '') {
    $query .= " AND w.price >= ?";
    $params[] = $price_min;
    $types .= 'd';
}

if ($price_max !== '') {
    $query .= " AND w.price <= ?";
    $params[] = $price_max;
    $types .= 'd';
}

if ($search) {
    $query .= " AND (w.name LIKE ? OR w.description LIKE ? OR w.brand LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'sss';
}

// Add sorting
switch ($sort) {
    case 'price_low':
        $query .= " ORDER BY w.price ASC";
        break;
    case 'price_high':
        $query .= " ORDER BY w.price DESC";
        break;
    case 'newest':
        $query .= " ORDER BY w.created_at DESC";
        break;
    case 'popular':
        $query .= " ORDER BY w.is_featured DESC, w.created_at DESC";
        break;
    default:
        $query .= " ORDER BY w.name ASC";
}

// Execute query
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get categories for filter
$categories_query = "SELECT * FROM categories WHERE status = 'active' ORDER BY name";
$categories_result = $conn->query($categories_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch Catalog | JackoTimespiece</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <style>
        :root {
            --gold: #c9b37e;
            --gold-dark: #a89a6a;
            --cream: #f5f5e6;
            --black: #000000;
            --dark-gray: #0c0c0c;
            --medium-gray: #181818;
            --light-gray: #333333;
        }
        
        .text-gold { color: var(--gold) !important; }
        .bg-gold { background: var(--gold) !important; }
        .border-gold { border-color: var(--gold) !important; }
        .font-serif { font-family: 'Playfair Display', serif; }
        .font-sans { font-family: 'Inter', sans-serif; }
        
        .catalog-container {
            background: linear-gradient(135deg, var(--black) 0%, var(--dark-gray) 100%);
            min-height: 100vh;
        }
        
        .glass-card {
            background: rgba(24, 24, 24, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(201, 179, 126, 0.2);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .product-card {
            background: rgba(12, 12, 12, 0.8);
            border: 2px solid rgba(201, 179, 126, 0.2);
            border-radius: 1.5rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .product-card:hover {
            border-color: var(--gold);
            background: rgba(12, 12, 12, 0.9);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(201, 179, 126, 0.2);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 1rem;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        
        .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--gold);
        }
        
        .original-price {
            text-decoration: line-through;
            color: rgba(255, 255, 255, 0.5);
            font-size: 1rem;
        }
        
        .badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--gold);
            color: var(--black);
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .filter-sidebar {
            background: rgba(12, 12, 12, 0.8);
            border: 1px solid rgba(201, 179, 126, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
        }
        
        .filter-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(24, 24, 24, 0.8);
            border: 1px solid rgba(201, 179, 126, 0.3);
            border-radius: 0.5rem;
            color: white;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        
        .filter-input:focus {
            border-color: var(--gold);
            outline: none;
            box-shadow: 0 0 0 2px rgba(201, 179, 126, 0.1);
        }
        
        .filter-button {
            background: var(--gold);
            color: var(--black);
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filter-button:hover {
            background: var(--gold-dark);
            transform: translateY(-1px);
        }
        
        .sort-select {
            background: rgba(24, 24, 24, 0.8);
            border: 1px solid rgba(201, 179, 126, 0.3);
            border-radius: 0.5rem;
            color: white;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .search-box {
            background: rgba(24, 24, 24, 0.8);
            border: 1px solid rgba(201, 179, 126, 0.3);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            color: white;
            font-size: 0.875rem;
            width: 100%;
            max-width: 300px;
        }
        
        .search-box:focus {
            border-color: var(--gold);
            outline: none;
            box-shadow: 0 0 0 2px rgba(201, 179, 126, 0.1);
        }
        
        .category-chip {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            background: rgba(201, 179, 126, 0.1);
            border: 1px solid rgba(201, 179, 126, 0.3);
            border-radius: 2rem;
            color: var(--gold);
            font-size: 0.75rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .category-chip:hover,
        .category-chip.active {
            background: var(--gold);
            color: var(--black);
            border-color: var(--gold);
        }
        
        .no-results {
            text-align: center;
            padding: 3rem;
            color: rgba(255, 255, 255, 0.6);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        .animate-delay-1 { animation-delay: 0.1s; }
        .animate-delay-2 { animation-delay: 0.2s; }
        .animate-delay-3 { animation-delay: 0.3s; }
        .animate-delay-4 { animation-delay: 0.4s; }
        .animate-delay-5 { animation-delay: 0.5s; }
    </style>
</head>
<body class="bg-black text-white font-sans">
    <div class="catalog-container py-16">
        <div class="container mx-auto px-6">
            <!-- Header -->
            <div class="text-center mb-12 animate-fade-in-up">
                <h1 class="text-4xl md:text-5xl font-serif text-gold mb-4">Watch Catalog</h1>
                <p class="text-gray-400 text-lg">Discover our exclusive collection of luxury timepieces</p>
            </div>
            
            <!-- Search and Sort Bar -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 animate-fade-in-up animate-delay-1">
                <div class="flex items-center space-x-4">
                    <input type="text" 
                           placeholder="Search watches..." 
                           value="<?= htmlspecialchars($search) ?>"
                           class="search-box"
                           onchange="updateSearch(this.value)">
                    <button onclick="clearFilters()" class="text-gold hover:text-white transition-colors">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
                
                <div class="flex items-center space-x-4">
                    <label class="text-gray-400">Sort by:</label>
                    <select class="sort-select" onchange="updateSort(this.value)">
                        <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name</option>
                        <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                        <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Popular</option>
                    </select>
                </div>
            </div>
            
            <!-- Category Chips -->
            <div class="mb-8 animate-fade-in-up animate-delay-2">
                <div class="flex flex-wrap justify-center gap-2">
                    <a href="?<?= http_build_query(array_merge($_GET, ['category' => ''])) ?>" 
                       class="category-chip <?= empty($category) ? 'active' : '' ?>">
                        All
                    </a>
                    <?php while ($cat = $categories_result->fetch_assoc()): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['category' => $cat['slug']])) ?>" 
                           class="category-chip <?= $category === $cat['slug'] ? 'active' : '' ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Filters Sidebar -->
                <div class="lg:col-span-1">
                    <div class="filter-sidebar sticky top-8 animate-fade-in-up animate-delay-3">
                        <h3 class="text-lg font-semibold text-gold mb-4">Filters</h3>
                        
                        <!-- Price Range -->
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-white mb-2">Price Range</h4>
                            <div class="space-y-2">
                                <input type="number" 
                                       placeholder="Min Price" 
                                       value="<?= htmlspecialchars($price_min) ?>"
                                       class="filter-input"
                                       onchange="updatePriceMin(this.value)">
                                <input type="number" 
                                       placeholder="Max Price" 
                                       value="<?= htmlspecialchars($price_max) ?>"
                                       class="filter-input"
                                       onchange="updatePriceMax(this.value)">
                            </div>
                        </div>
                        
                        <!-- Categories -->
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-white mb-2">Categories</h4>
                            <div class="space-y-2">
                                <?php 
                                $categories_result->data_seek(0);
                                while ($cat = $categories_result->fetch_assoc()): 
                                ?>
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" 
                                               value="<?= $cat['slug'] ?>"
                                               <?= $category === $cat['slug'] ? 'checked' : '' ?>
                                               onchange="updateCategory(this.value, this.checked)"
                                               class="text-gold">
                                        <span class="text-sm text-gray-300"><?= htmlspecialchars($cat['name']) ?></span>
                                    </label>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        
                        <!-- Features -->
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-white mb-2">Features</h4>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" class="text-gold">
                                    <span class="text-sm text-gray-300">Featured</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" class="text-gold">
                                    <span class="text-sm text-gray-300">New Arrivals</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" class="text-gold">
                                    <span class="text-sm text-gray-300">Limited Edition</span>
                                </label>
                            </div>
                        </div>
                        
                        <button onclick="applyFilters()" class="filter-button w-full">
                            Apply Filters
                        </button>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <div class="lg:col-span-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <?php 
                        $product_count = 0;
                        while ($watch = $result->fetch_assoc()): 
                            $product_count++;
                        ?>
                            <div class="product-card animate-fade-in-up animate-delay-<?= min($product_count, 5) ?>">
                                <?php if ($watch['is_featured']): ?>
                                    <div class="badge">Featured</div>
                                <?php endif; ?>
                                <?php if ($watch['is_limited_edition']): ?>
                                    <div class="badge" style="background: #ef4444;">Limited</div>
                                <?php endif; ?>
                                <?php if ($watch['is_new_arrival']): ?>
                                    <div class="badge" style="background: #10b981;">New</div>
                                <?php endif; ?>
                                
                                <img src="../assets/images/watches/<?= htmlspecialchars($watch['image']) ?>" 
                                     alt="<?= htmlspecialchars($watch['name']) ?>" 
                                     class="product-image"
                                     onerror="this.src='/assets/images/watches/default.jpg'">
                                
                                <div class="space-y-2">
                                    <h3 class="text-lg font-semibold text-white"><?= htmlspecialchars($watch['name']) ?></h3>
                                    <p class="text-sm text-gray-400"><?= htmlspecialchars(mb_strimwidth($watch['description'], 0, 80, '...')) ?></p>
                                    
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="price">₹<?= number_format($watch['price']) ?></div>
                                            <?php if ($watch['original_price'] && $watch['original_price'] > $watch['price']): ?>
                                                <div class="original-price">₹<?= number_format($watch['original_price']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <span class="text-xs text-gray-400"><?= htmlspecialchars($watch['category_name']) ?></span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between mt-4">
                                        <span class="text-xs text-gray-400">
                                            <i class="fas fa-box mr-1"></i>
                                            <?= $watch['stock_quantity'] ?> in stock
                                        </span>
                                        <div class="flex space-x-2">
                                            <button onclick="addToWishlist(<?= $watch['id'] ?>)" 
                                                    class="text-gray-400 hover:text-gold transition-colors">
                                                <i class="fas fa-heart"></i>
                                            </button>
                                            <button onclick="addToCart(<?= $watch['id'] ?>)" 
                                                    class="bg-gold text-black px-3 py-1 rounded text-sm font-semibold hover:bg-white transition-colors">
                                                Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        
                        <?php if ($product_count === 0): ?>
                            <div class="col-span-full no-results">
                                <i class="fas fa-search text-4xl text-gray-600 mb-4"></i>
                                <h3 class="text-xl font-semibold mb-2">No watches found</h3>
                                <p class="text-gray-400">Try adjusting your filters or search terms</p>
                                <button onclick="clearFilters()" class="mt-4 bg-gold text-black px-6 py-2 rounded font-semibold hover:bg-white transition-colors">
                                    Clear All Filters
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Results Count -->
                    <?php if ($product_count > 0): ?>
                        <div class="text-center mt-8 text-gray-400">
                            Showing <?= $product_count ?> watch<?= $product_count !== 1 ? 'es' : '' ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateSearch(value) {
            const url = new URL(window.location);
            if (value) {
                url.searchParams.set('search', value);
            } else {
                url.searchParams.delete('search');
            }
            window.location.href = url.toString();
        }
        
        function updateSort(value) {
            const url = new URL(window.location);
            url.searchParams.set('sort', value);
            window.location.href = url.toString();
        }
        
        function updatePriceMin(value) {
            const url = new URL(window.location);
            if (value) {
                url.searchParams.set('price_min', value);
            } else {
                url.searchParams.delete('price_min');
            }
            window.location.href = url.toString();
        }
        
        function updatePriceMax(value) {
            const url = new URL(window.location);
            if (value) {
                url.searchParams.set('price_max', value);
            } else {
                url.searchParams.delete('price_max');
            }
            window.location.href = url.toString();
        }
        
        function updateCategory(value, checked) {
            const url = new URL(window.location);
            if (checked) {
                url.searchParams.set('category', value);
            } else {
                url.searchParams.delete('category');
            }
            window.location.href = url.toString();
        }
        
        function clearFilters() {
            window.location.href = window.location.pathname;
        }
        
        function applyFilters() {
            // This function can be used for more complex filtering
            // For now, individual filter changes update immediately
        }
        
        function addToCart(watchId) {
            <?php if (!isset($_SESSION['user'])): ?>
                // Redirect to login if user is not logged in
                window.location.href = '<?= LOGIN_PAGE ?>';
                return;
            <?php endif; ?>
            
            // Add to cart functionality
            fetch('../api/cart/add.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `watch_id=${watchId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.error || 'Error adding to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error adding to cart', 'error');
            });
        }
        
        function addToWishlist(watchId) {
            <?php if (!isset($_SESSION['user'])): ?>
                // Redirect to login if user is not logged in
                window.location.href = '<?= LOGIN_PAGE ?>';
                return;
            <?php endif; ?>
            
            const button = document.querySelector(`button[onclick="addToWishlist(${watchId})"]`);
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
                        icon.classList.add('text-gray-400');
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
                        icon.classList.remove('text-gray-400');
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
                            const button = document.querySelector(`button[onclick="addToWishlist(${item.watch_id})"]`);
                            if (button) {
                                const icon = button.querySelector('i');
                                icon.classList.remove('text-gray-400');
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
        
        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.product-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease-out';
            observer.observe(el);
        });
    </script>
</body>
</html>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 