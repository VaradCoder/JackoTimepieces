<?php
session_start();
require_once '../../core/config/constants.php';
if (!isset($_SESSION['user'])) header('Location: ' . LOGIN_PAGE);
require_once '../../core/db/connection.php';

$user_id = $_SESSION['user']['id'];

// Get wishlist items
$stmt = $conn->prepare("
    SELECT w.*, wl.id as wishlist_id 
    FROM wishlist wl 
    JOIN watches w ON wl.watch_id = w.id 
    WHERE wl.user_id = ? 
    ORDER BY wl.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wishlist_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist | JackoTimespiece</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
</head>
<body class="bg-black text-white font-sans min-h-screen">
    <?php require_once '../../templates/header.php'; ?>
    
    <section class="container mx-auto px-6 py-16">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-serif text-gold mb-2">My Wishlist</h1>
                <p class="text-gray-400">Your saved items for future purchase</p>
            </div>

            <!-- Message -->
            <?php if ($message): ?>
                <div class="bg-gold text-black px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-info-circle mr-2"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Wishlist Items -->
            <?php if (empty($wishlist_items)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-heart text-gray-600 text-6xl mb-4"></i>
                    <h3 class="text-xl text-gray-400 mb-4">Your wishlist is empty</h3>
                    <p class="text-gray-500 mb-6">Start adding products to your wishlist to see them here.</p>
                    <a href="../../public/store.php" class="bg-gold text-black px-6 py-3 rounded-lg hover:bg-white hover:text-gold transition-colors duration-300">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        Browse Products
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($wishlist_items as $item): ?>
                        <div class="bg-gray-900 rounded-lg border border-gray-800 p-6 hover:border-gold transition-colors duration-300 wishlist-item" data-watch-id="<?php echo $item['id']; ?>">
                            <!-- Product Image -->
                            <div class="relative mb-4">
                                <img src="../../assets/images/watches/<?php echo htmlspecialchars($item['main_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     class="w-full h-48 object-cover rounded-lg"
                                     onerror="this.onerror=null;this.src='../../assets/images/watches/default.jpg';">
                                
                                <!-- Badges -->
                                <div class="absolute top-2 left-2">
                                    <?php if ($item['is_featured']): ?>
                                        <span class="bg-gold text-black text-xs px-2 py-1 rounded-full font-bold">Featured</span>
                                    <?php endif; ?>
                                    <?php if ($item['is_limited']): ?>
                                        <span class="bg-red-600 text-white text-xs px-2 py-1 rounded-full font-bold ml-1">Limited</span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Quick Actions -->
                                <div class="absolute top-2 right-2 flex space-x-2">
                                    <button onclick="quickAddToCart(<?php echo $item['id']; ?>)" 
                                            class="bg-gold text-black p-2 rounded-full hover:bg-white transition-colors duration-300"
                                            title="Quick Add to Cart">
                                        <i class="fas fa-shopping-cart text-sm"></i>
                                    </button>
                                    <button onclick="removeFromWishlist(<?php echo $item['id']; ?>)" 
                                            class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition-colors duration-300"
                                            title="Remove from Wishlist">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-white mb-2"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="text-gray-400 text-sm mb-2"><?php echo htmlspecialchars($item['brand']); ?></p>
                                <p class="text-gray-500 text-sm mb-3 line-clamp-2"><?php echo htmlspecialchars($item['short_description']); ?></p>
                                
                                <!-- Price -->
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <?php if ($item['sale_price']): ?>
                                            <span class="text-lg font-bold text-gold">₹<?php echo number_format($item['sale_price']); ?></span>
                                            <span class="text-gray-500 line-through ml-2">₹<?php echo number_format($item['price']); ?></span>
                                        <?php else: ?>
                                            <span class="text-lg font-bold text-gold">₹<?php echo number_format($item['price']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-xs text-gray-500"><?php echo $item['stock_quantity']; ?> in stock</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-3">
                                <a href="../../public/watch.php?id=<?php echo $item['id']; ?>" 
                                   class="flex-1 bg-transparent border border-gold text-gold py-2 px-4 rounded-lg hover:bg-gold hover:text-black transition-colors duration-300 text-center">
                                    <i class="fas fa-eye mr-2"></i>
                                    View Details
                                </a>
                                <button onclick="quickAddToCart(<?php echo $item['id']; ?>)" 
                                        class="flex-1 bg-gold text-black py-2 px-4 rounded-lg hover:bg-white hover:text-gold transition-colors duration-300">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Summary -->
                <div class="mt-8 bg-gray-900 rounded-lg border border-gray-800 p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-400">Total items in wishlist: <span class="text-gold font-semibold wishlist-count"><?php echo count($wishlist_items); ?></span></p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="addAllToCart()" class="bg-gold text-black px-6 py-2 rounded-lg hover:bg-white hover:text-gold transition-colors duration-300">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Add All to Cart
                            </button>
                            <button onclick="clearWishlist()" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors duration-300">
                                <i class="fas fa-trash mr-2"></i>
                                Clear Wishlist
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
        // Quick add to cart
        function quickAddToCart(watchId) {
            fetch('../../api/cart/add.php', {
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
                    showNotification(data.error || 'Failed to add to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to add to cart', 'error');
            });
        }

        // Remove from wishlist
        function removeFromWishlist(watchId) {
            if (confirm('Are you sure you want to remove this item from your wishlist?')) {
                fetch('../../api/wishlist/remove.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `watch_id=${watchId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the item from the DOM
                        const item = document.querySelector(`[data-watch-id="${watchId}"]`);
                        if (item) {
                            item.remove();
                            showNotification(data.message, 'success');
                            // Update wishlist count
                            updateWishlistCount();
                        }
                    } else {
                        showNotification(data.error || 'Failed to remove from wishlist', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to remove from wishlist', 'error');
                });
            }
        }

        // Add all to cart
        function addAllToCart() {
            if (confirm('Add all wishlist items to cart?')) {
                const wishlistItems = document.querySelectorAll('.wishlist-item');
                let addedCount = 0;
                let totalItems = wishlistItems.length;
                
                wishlistItems.forEach((item, index) => {
                    const watchId = item.getAttribute('data-watch-id');
                    
                    fetch('../../api/cart/add.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `watch_id=${watchId}&quantity=1`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            addedCount++;
                        }
                        
                        if (addedCount === totalItems) {
                            showNotification(`Successfully added ${addedCount} items to cart!`, 'success');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });
            }
        }

        // Clear wishlist
        function clearWishlist() {
            if (confirm('Are you sure you want to clear your entire wishlist?')) {
                fetch('../../api/wishlist/clear.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Clear all wishlist items from DOM
                        const wishlistContainer = document.querySelector('.grid');
                        if (wishlistContainer) {
                            wishlistContainer.innerHTML = `
                                <div class="col-span-full text-center py-12">
                                    <i class="fas fa-heart text-gray-600 text-6xl mb-4"></i>
                                    <h3 class="text-xl text-gray-400 mb-4">Your wishlist is empty</h3>
                                    <p class="text-gray-500 mb-6">Start adding products to your wishlist to see them here.</p>
                                    <a href="../../public/store.php" class="bg-gold text-black px-6 py-3 rounded-lg hover:bg-white hover:text-gold transition-colors duration-300">
                                        <i class="fas fa-shopping-bag mr-2"></i>
                                        Browse Products
                                    </a>
                                </div>
                            `;
                        }
                        showNotification(data.message, 'success');
                    } else {
                        showNotification(data.error || 'Failed to clear wishlist', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to clear wishlist', 'error');
                });
            }
        }

        // Update wishlist count
        function updateWishlistCount() {
            const wishlistItems = document.querySelectorAll('.wishlist-item');
            const countElement = document.querySelector('.wishlist-count');
            if (countElement) {
                countElement.textContent = wishlistItems.length;
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

        // Animate wishlist items
        if (window.anime) {
            anime({
                targets: '.wishlist-item',
                opacity: [0, 1],
                translateY: [40, 0],
                delay: anime.stagger(100),
                duration: 700,
                easing: 'easeOutCubic'
            });
        }
    </script>

    <?php require_once '../../templates/footer.php'; ?>
</body>
</html> 