<?php
session_start();
require_once __DIR__ . '/../../core/config/constants.php';
require_once __DIR__ . '/../../core/db/connection.php';
require_once __DIR__ . '/../../templates/header.php';

// Fetch current offers from database
$sql = "SELECT * FROM coupons WHERE status = 'active' AND expiry_date >= CURDATE() ORDER BY discount_percentage DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Offers | JackoTimespiece</title>
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
        .offers-hero {
            background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(201,179,126,0.1) 100%);
            position: relative;
            overflow: hidden;
        }
        .offers-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('/assets/images/brand/offers-bg.jpg') center/cover;
            opacity: 0.3;
            z-index: -1;
        }
        .offer-card {
            background: rgba(24,24,24,0.9);
            border: 2px solid #c9b37e;
            border-radius: 1.5rem;
            padding: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .offer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(201,179,126,0.3);
        }
        .offer-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #c9b37e, #dcdcdc, #c9b37e);
        }
        .discount-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #c9b37e;
            color: #000;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .expiry-badge {
            position: absolute;
            bottom: 1rem;
            right: 1rem;
            background: rgba(239,68,68,0.9);
            color: #fff;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
        }
        .featured-offer {
            border-width: 3px;
            background: rgba(201,179,126,0.05);
        }
        .featured-offer::before {
            height: 6px;
        }
        .countdown {
            background: rgba(201,179,126,0.1);
            border: 1px solid #c9b37e;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1rem;
        }
        .countdown-item {
            text-align: center;
            margin: 0 0.5rem;
        }
        .countdown-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #c9b37e;
        }
        .countdown-label {
            font-size: 0.8rem;
            color: #9ca3af;
        }
    </style>
</head>
<body class="bg-black text-white font-sans">
    
    <!-- Hero Section -->
    <section class="offers-hero py-20 relative">
        <div class="container mx-auto px-6 text-center relative z-10">
            <h1 class="text-4xl md:text-6xl font-serif text-gold mb-6">Current Offers</h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                Discover exclusive deals and special promotions on luxury timepieces. Limited time offers for our valued customers.
            </p>
        </div>
    </section>

    <!-- Offers Content -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-6">
            
            <!-- Featured Offer -->
            <div class="mb-16">
                <h2 class="text-3xl font-serif text-gold mb-8 text-center">Featured Offer</h2>
                <div class="featured-offer offer-card max-w-4xl mx-auto">
                    <div class="discount-badge">50% OFF</div>
                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <div>
                            <h3 class="text-2xl font-serif text-gold mb-4">Luxury Collection Sale</h3>
                            <p class="text-gray-300 mb-4">
                                Celebrate our anniversary with an exclusive 50% discount on selected luxury timepieces. 
                                This limited-time offer includes premium brands and limited edition pieces.
                            </p>
                            <ul class="text-gray-300 space-y-2 mb-6">
                                <li><i class="fas fa-check text-gold mr-2"></i>Valid on all luxury brands</li>
                                <li><i class="fas fa-check text-gold mr-2"></i>Includes limited editions</li>
                                <li><i class="fas fa-check text-gold mr-2"></i>Free shipping included</li>
                                <li><i class="fas fa-check text-gold mr-2"></i>Extended warranty available</li>
                            </ul>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="<?= STORE_PAGE ?>" class="bg-gold text-black px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-gold transition-colors duration-300 text-center">
                                    Shop Now
                                </a>
                                <button class="border border-gold text-gold px-8 py-3 rounded-full font-semibold hover:bg-gold hover:text-black transition-colors duration-300">
                                    Get Coupon
                                </button>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="countdown">
                                <p class="text-gold font-semibold mb-3">Offer Ends In:</p>
                                <div class="flex justify-center">
                                    <div class="countdown-item">
                                        <div class="countdown-number" id="days">05</div>
                                        <div class="countdown-label">Days</div>
                                    </div>
                                    <div class="countdown-item">
                                        <div class="countdown-number" id="hours">12</div>
                                        <div class="countdown-label">Hours</div>
                                    </div>
                                    <div class="countdown-item">
                                        <div class="countdown-number" id="minutes">30</div>
                                        <div class="countdown-label">Minutes</div>
                                    </div>
                                    <div class="countdown-item">
                                        <div class="countdown-number" id="seconds">45</div>
                                        <div class="countdown-label">Seconds</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="expiry-badge">Expires: Dec 31, 2024</div>
                </div>
            </div>

            <!-- All Offers Grid -->
            <div class="mb-16">
                <h2 class="text-3xl font-serif text-gold mb-8 text-center">All Current Offers</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($offer = $result->fetch_assoc()):
                            $expiry_date = new DateTime($offer['expiry_date']);
                            $today = new DateTime();
                            $days_left = $today->diff($expiry_date)->days;
                    ?>
                        <div class="offer-card">
                            <div class="discount-badge"><?= $offer['discount_percentage'] ?>% OFF</div>
                            <h3 class="text-xl font-serif text-gold mb-3"><?= htmlspecialchars($offer['name']) ?></h3>
                            <p class="text-gray-300 mb-4"><?= htmlspecialchars($offer['description']) ?></p>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Code:</span>
                                    <span class="text-gold font-mono font-bold"><?= htmlspecialchars($offer['code']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Min. Purchase:</span>
                                    <span class="text-white">₹<?= number_format($offer['minimum_amount']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Max. Discount:</span>
                                    <span class="text-white">₹<?= number_format($offer['maximum_discount']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Days Left:</span>
                                    <span class="text-white"><?= $days_left ?> days</span>
                                </div>
                            </div>
                            
                            <div class="flex gap-3">
                                <button class="flex-1 bg-gold text-black px-4 py-2 rounded-full font-semibold hover:bg-white hover:text-gold transition-colors duration-300">
                                    Copy Code
                                </button>
                                <a href="<?= STORE_PAGE ?>" class="flex-1 border border-gold text-gold px-4 py-2 rounded-full font-semibold hover:bg-gold hover:text-black transition-colors duration-300 text-center">
                                    Shop Now
                                </a>
                            </div>
                            
                            <div class="expiry-badge">Expires: <?= date('M d, Y', strtotime($offer['expiry_date'])) ?></div>
                        </div>
                    <?php
                        endwhile;
                    } else {
                        echo '<div class="col-span-full text-center text-gray-400 py-12">';
                        echo '<i class="fas fa-tag text-4xl text-gold mb-4"></i>';
                        echo '<p class="text-xl mb-2">No current offers available</p>';
                        echo '<p class="text-gray-500">Check back soon for new promotions!</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

            <!-- Special Promotions -->
            <div class="mb-16">
                <h2 class="text-3xl font-serif text-gold mb-8 text-center">Special Promotions</h2>
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="glass p-8">
                        <div class="text-center mb-6">
                            <i class="fas fa-gift text-4xl text-gold mb-4"></i>
                            <h3 class="text-xl font-serif text-gold mb-2">First Purchase Bonus</h3>
                        </div>
                        <p class="text-gray-300 mb-4">
                            New customers get an additional 10% off their first purchase. 
                            Combine with existing offers for maximum savings.
                        </p>
                        <ul class="text-gray-300 space-y-2 mb-6">
                            <li><i class="fas fa-check text-gold mr-2"></i>Valid for first-time buyers</li>
                            <li><i class="fas fa-check text-gold mr-2"></i>Stackable with other offers</li>
                            <li><i class="fas fa-check text-gold mr-2"></i>No minimum purchase required</li>
                        </ul>
                        <a href="<?= STORE_PAGE ?>" class="bg-gold text-black px-6 py-2 rounded-full font-semibold hover:bg-white hover:text-gold transition-colors duration-300 block text-center">
                            Start Shopping
                        </a>
                    </div>
                    
                    <div class="glass p-8">
                        <div class="text-center mb-6">
                            <i class="fas fa-shipping-fast text-4xl text-gold mb-4"></i>
                            <h3 class="text-xl font-serif text-gold mb-2">Free Premium Shipping</h3>
                        </div>
                        <p class="text-gray-300 mb-4">
                            Free express shipping on all orders above ₹50,000. 
                            Includes insurance and tracking for peace of mind.
                        </p>
                        <ul class="text-gray-300 space-y-2 mb-6">
                            <li><i class="fas fa-check text-gold mr-2"></i>Orders above ₹50,000</li>
                            <li><i class="fas fa-check text-gold mr-2"></i>Express delivery</li>
                            <li><i class="fas fa-check text-gold mr-2"></i>Full insurance coverage</li>
                        </ul>
                        <a href="<?= STORE_PAGE ?>" class="bg-gold text-black px-6 py-2 rounded-full font-semibold hover:bg-white hover:text-gold transition-colors duration-300 block text-center">
                            Browse Collection
                        </a>
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="glass p-8">
                <h3 class="text-xl font-serif text-gold mb-4">Terms & Conditions</h3>
                <div class="text-gray-300 space-y-3 text-sm">
                    <p><strong class="text-gold">Offer Validity:</strong> All offers are valid until the specified expiry date or while stocks last.</p>
                    <p><strong class="text-gold">Stacking:</strong> Only one coupon code can be used per order unless otherwise specified.</p>
                    <p><strong class="text-gold">Exclusions:</strong> Some offers may not apply to limited edition or custom pieces.</p>
                    <p><strong class="text-gold">Modifications:</strong> JackoTimespiece reserves the right to modify or cancel offers at any time.</p>
                    <p><strong class="text-gold">Returns:</strong> Discounted items are subject to our standard return policy.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Signup -->
    <section class="py-20 bg-[#0c0c0c]">
        <div class="container mx-auto px-6 text-center">
            <div class="glass p-12 max-w-3xl mx-auto">
                <h2 class="text-3xl font-serif text-gold mb-6">Stay Updated</h2>
                <p class="text-gray-300 text-lg mb-8">
                    Subscribe to our newsletter to be the first to know about new offers, exclusive deals, and limited-time promotions.
                </p>
                <form class="flex flex-col sm:flex-row gap-4 justify-center max-w-md mx-auto">
                    <input type="email" placeholder="Enter your email" 
                           class="flex-1 px-4 py-3 rounded-full border border-gold bg-black text-white focus:outline-none focus:border-white">
                    <button type="submit" class="bg-gold text-black px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-gold transition-colors duration-300">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </section>

    <?php require_once __DIR__ . '/../../templates/footer.php'; ?>

    <script>
        // Countdown timer for featured offer
        function updateCountdown() {
            const now = new Date();
            const endDate = new Date('2024-12-31T23:59:59');
            const diff = endDate - now;
            
            if (diff > 0) {
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                
                document.getElementById('days').textContent = days.toString().padStart(2, '0');
                document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
                document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
                document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
            }
        }
        
        // Update countdown every second
        setInterval(updateCountdown, 1000);
        updateCountdown();
        
        // Copy coupon code functionality
        document.querySelectorAll('button').forEach(button => {
            if (button.textContent.includes('Copy Code')) {
                button.addEventListener('click', function() {
                    const code = this.closest('.offer-card').querySelector('.font-mono').textContent;
                    navigator.clipboard.writeText(code).then(() => {
                        this.textContent = 'Copied!';
                        this.style.background = '#10b981';
                        setTimeout(() => {
                            this.textContent = 'Copy Code';
                            this.style.background = '';
                        }, 2000);
                    });
                });
            }
        });
        
        // Animate elements on page load
        if (window.anime) {
            anime({
                targets: '.offers-hero h1, .offers-hero p',
                opacity: [0, 1],
                translateY: [30, 0],
                delay: anime.stagger(200),
                duration: 1000,
                easing: 'easeOutCubic'
            });
            
            anime({
                targets: '.offer-card',
                opacity: [0, 1],
                translateY: [40, 0],
                delay: anime.stagger(150),
                duration: 800,
                easing: 'easeOutCubic'
            });
        }
    </script>
</body>
</html> 