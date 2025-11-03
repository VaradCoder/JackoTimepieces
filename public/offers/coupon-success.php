<?php
session_start();
require_once __DIR__ . '/../../core/config/constants.php';
require_once __DIR__ . '/../../templates/header.php';

$coupon_code = $_GET['code'] ?? '';
$discount_amount = $_GET['discount'] ?? '';
$message = $_GET['message'] ?? 'Coupon applied successfully!';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupon Applied | JackoTimespiece</title>
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
        .success-hero {
            background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(201,179,126,0.1) 100%);
            position: relative;
            overflow: hidden;
        }
        .success-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('/assets/images/brand/success-bg.jpg') center/cover;
            opacity: 0.3;
            z-index: -1;
        }
        .success-card {
            background: rgba(24,24,24,0.9);
            border: 2px solid #c9b37e;
            border-radius: 1.5rem;
            padding: 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #c9b37e, #10b981);
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: #fff;
            font-size: 3rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .discount-badge {
            background: #c9b37e;
            color: #000;
            padding: 0.5rem 1.5rem;
            border-radius: 2rem;
            font-weight: bold;
            font-size: 1.2rem;
            display: inline-block;
            margin: 1rem 0;
        }
        .next-steps {
            background: rgba(201,179,126,0.05);
            border: 1px solid #c9b37e;
            border-radius: 1rem;
            padding: 2rem;
            margin-top: 2rem;
        }
        .step-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 1rem;
            background: rgba(24,24,24,0.5);
            border-radius: 0.5rem;
        }
        .step-number {
            width: 40px;
            height: 40px;
            background: #c9b37e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-weight: bold;
            margin-right: 1rem;
        }
    </style>
</head>
<body class="bg-black text-white font-sans">
    
    <!-- Hero Section -->
    <section class="success-hero py-20 relative">
        <div class="container mx-auto px-6 text-center relative z-10">
            <h1 class="text-4xl md:text-6xl font-serif text-gold mb-6">Coupon Applied!</h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                Your discount has been successfully applied to your order.
            </p>
        </div>
    </section>

    <!-- Success Content -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-6">
            <div class="max-w-2xl mx-auto">
                
                <!-- Success Card -->
                <div class="success-card mb-12">
                    <div class="success-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    
                    <h2 class="text-2xl font-serif text-gold mb-4">Congratulations!</h2>
                    <p class="text-gray-300 text-lg mb-6"><?= htmlspecialchars($message) ?></p>
                    
                    <?php if ($coupon_code): ?>
                    <div class="mb-6">
                        <p class="text-gray-400 mb-2">Coupon Code Applied:</p>
                        <div class="discount-badge"><?= htmlspecialchars($coupon_code) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($discount_amount): ?>
                    <div class="mb-6">
                        <p class="text-gray-400 mb-2">Discount Amount:</p>
                        <p class="text-2xl font-bold text-gold">₹<?= number_format($discount_amount) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="<?= CART_PAGE ?>" class="bg-gold text-black px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-gold transition-colors duration-300">
                            View Cart
                        </a>
                        <a href="<?= STORE_PAGE ?>" class="border border-gold text-gold px-8 py-3 rounded-full font-semibold hover:bg-gold hover:text-black transition-colors duration-300">
                            Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="next-steps">
                    <h3 class="text-xl font-serif text-gold mb-4">What's Next?</h3>
                    
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div>
                            <h4 class="text-gold font-semibold">Review Your Cart</h4>
                            <p class="text-gray-300 text-sm">Check that your discount has been applied correctly</p>
                        </div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div>
                            <h4 class="text-gold font-semibold">Add More Items</h4>
                            <p class="text-gray-300 text-sm">Continue shopping to maximize your savings</p>
                        </div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div>
                            <h4 class="text-gold font-semibold">Proceed to Checkout</h4>
                            <p class="text-gray-300 text-sm">Complete your purchase with secure payment</p>
                        </div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">4</div>
                        <div>
                            <h4 class="text-gold font-semibold">Enjoy Your Purchase</h4>
                            <p class="text-gray-300 text-sm">Receive your luxury timepiece with premium service</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Offers -->
                <div class="glass p-8 mt-12 text-center">
                    <h3 class="text-xl font-serif text-gold mb-4">More Savings Await</h3>
                    <p class="text-gray-300 mb-6">
                        Discover more exclusive offers and promotions to enhance your shopping experience.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="current.php" class="bg-gold text-black px-6 py-2 rounded-full font-semibold hover:bg-white hover:text-gold transition-colors duration-300">
                            View All Offers
                        </a>
                        <a href="<?= CONTACT_PAGE ?>" class="border border-gold text-gold px-6 py-2 rounded-full font-semibold hover:bg-gold hover:text-black transition-colors duration-300">
                            Get Help
                        </a>
                    </div>
                </div>

                <!-- Terms Reminder -->
                <div class="mt-8 p-6 bg-gray-900 rounded-lg">
                    <h4 class="text-gold font-semibold mb-2">Important Notes:</h4>
                    <ul class="text-gray-300 text-sm space-y-1">
                        <li>• Coupon codes are valid for one-time use per customer</li>
                        <li>• Discounts cannot be combined unless specified</li>
                        <li>• Offers are subject to terms and conditions</li>
                        <li>• Prices and availability may change without notice</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions -->
    <section class="py-20 bg-[#0c0c0c]">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="glass p-6 text-center">
                    <i class="fas fa-shipping-fast text-3xl text-gold mb-4"></i>
                    <h3 class="text-lg font-serif text-gold mb-2">Free Shipping</h3>
                    <p class="text-gray-300 text-sm">On orders above ₹50,000</p>
                </div>
                
                <div class="glass p-6 text-center">
                    <i class="fas fa-shield-alt text-3xl text-gold mb-4"></i>
                    <h3 class="text-lg font-serif text-gold mb-2">Secure Payment</h3>
                    <p class="text-gray-300 text-sm">100% secure checkout</p>
                </div>
                
                <div class="glass p-6 text-center">
                    <i class="fas fa-undo text-3xl text-gold mb-4"></i>
                    <h3 class="text-lg font-serif text-gold mb-2">Easy Returns</h3>
                    <p class="text-gray-300 text-sm">30-day return policy</p>
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
                    targets: '.success-hero h1, .success-hero p',
                    opacity: [0, 1],
                    translateY: [30, 0],
                    delay: anime.stagger(200),
                    duration: 1000,
                    easing: 'easeOutCubic'
                });
                
                // Success card animation
                anime({
                    targets: '.success-card',
                    opacity: [0, 1],
                    scale: [0.9, 1],
                    duration: 800,
                    easing: 'easeOutBack'
                });
                
                // Step items animation
                anime({
                    targets: '.step-item',
                    opacity: [0, 1],
                    translateX: [-30, 0],
                    delay: anime.stagger(200),
                    duration: 600,
                    easing: 'easeOutCubic'
                });
                
                // Success icon pulse
                anime({
                    targets: '.success-icon',
                    scale: [1, 1.1, 1],
                    duration: 2000,
                    loop: true,
                    easing: 'easeInOutQuad'
                });
            }
        });
        
        // Auto-redirect to cart after 5 seconds
        setTimeout(function() {
            if (confirm('Would you like to view your cart now?')) {
                window.location.href = '<?= CART_PAGE ?>';
            }
        }, 5000);
    </script>
</body>
</html> 