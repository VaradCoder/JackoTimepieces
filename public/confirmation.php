<?php
session_start();

// Check if order confirmation data exists - do this BEFORE including header template
if (!isset($_SESSION['order_confirmation'])) {
    header('Location: ../public/cart.php');
    exit;
}

require_once __DIR__ . '/../core/config/constants.php';
require_once __DIR__ . '/../templates/header.php';

// Get order details from session
$order_confirmation = $_SESSION['order_confirmation'];
$order_id = $order_confirmation['order_id'];
$order_db_id = $order_confirmation['order_db_id'];
$customer_name = $order_confirmation['customer_name'];
$total = $order_confirmation['total'];
$payment_method = $order_confirmation['payment_method'];
$status = $order_confirmation['status'];

// Clear the confirmation data after displaying
unset($_SESSION['order_confirmation']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation | JackoTimespiece</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
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
        .confirmation-hero {
            background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(201,179,126,0.1) 100%);
            position: relative;
            overflow: hidden;
        }
        .confirmation-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('/assets/images/brand/confirmation-bg.jpg') center/cover;
            opacity: 0.3;
            z-index: -1;
        }
        .confirmation-card {
            background: rgba(24,24,24,0.9);
            border: 2px solid #c9b37e;
            border-radius: 1.5rem;
            padding: 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .confirmation-card::before {
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
        .order-details {
            background: rgba(201,179,126,0.05);
            border: 1px solid #c9b37e;
            border-radius: 1rem;
            padding: 2rem;
            margin: 2rem 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(201,179,126,0.2);
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .order-number {
            background: #c9b37e;
            color: #000;
            padding: 0.5rem 1.5rem;
            border-radius: 2rem;
            font-weight: bold;
            font-size: 1.1rem;
            display: inline-block;
            margin: 1rem 0;
        }
        .next-steps {
            background: rgba(24,24,24,0.5);
            border: 1px solid #c9b37e;
            border-radius: 1rem;
            padding: 2rem;
            margin-top: 2rem;
        }
        .step-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
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
        .timeline {
            position: relative;
            padding-left: 2rem;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 0.75rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #c9b37e;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -1.5rem;
            top: 0.5rem;
            width: 12px;
            height: 12px;
            background: #c9b37e;
            border-radius: 50%;
            border: 3px solid #000;
        }
    </style>
</head>
<body class="bg-black text-white font-sans">
    
    <!-- Hero Section -->
    <section class="confirmation-hero py-20 relative">
        <div class="container mx-auto px-6 text-center relative z-10">
            <h1 class="text-4xl md:text-6xl font-serif text-gold mb-6">Order Confirmed!</h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                Thank you for your purchase. Your order has been successfully placed and confirmed.
            </p>
        </div>
    </section>

    <!-- Confirmation Content -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                
                <!-- Confirmation Card -->
                <div class="confirmation-card mb-12">
                    <div class="success-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    
                    <h2 class="text-2xl font-serif text-gold mb-4">Thank You!</h2>
                    <p class="text-gray-300 text-lg mb-6">
                        Your order has been successfully placed. We've sent a confirmation email with all the details.
                    </p>
                    
                    <div class="order-number"><?= htmlspecialchars($order_id) ?></div>
                    
                    <div class="order-details">
                        <div class="detail-row">
                            <span class="text-gray-400">Customer:</span>
                            <span class="text-white font-semibold"><?= htmlspecialchars($customer_name) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="text-gray-400">Order Total:</span>
                            <span class="text-gold font-bold text-lg">₹<?= number_format($total) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="text-gray-400">Payment Method:</span>
                            <span class="text-white"><?= ucfirst(htmlspecialchars($payment_method)) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="text-gray-400">Status:</span>
                            <span class="<?= $status === 'paid' ? 'text-green-400' : 'text-yellow-400' ?> font-semibold">
                                <?= ucfirst(htmlspecialchars($status)) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center mt-6">
                        <a href="<?= ACCOUNT_ORDERS ?>" class="bg-gold text-black px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-gold transition-colors duration-300">
                            View Orders
                        </a>
                        <a href="<?= STORE_PAGE ?>" class="border border-gold text-gold px-8 py-3 rounded-full font-semibold hover:bg-gold hover:text-black transition-colors duration-300">
                            Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="order-details">
                    <h3 class="text-xl font-serif text-gold mb-4">Order Summary</h3>
                    
                    <div class="detail-row">
                        <span class="text-gray-400">Order Number:</span>
                        <span class="text-white font-semibold"><?= htmlspecialchars($order_id) ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="text-gray-400">Order Date:</span>
                        <span class="text-white"><?= date('F d, Y') ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="text-gray-400">Payment Method:</span>
                        <span class="text-white"><?= htmlspecialchars($payment_method) ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="text-gray-400">Order Total:</span>
                        <span class="text-gold font-bold text-xl">₹<?= number_format($order_total) ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="text-gray-400">Status:</span>
                        <span class="text-green-400 font-semibold">Confirmed</span>
                    </div>
                </div>

                <!-- Order Timeline -->
                <div class="glass p-8 mt-8">
                    <h3 class="text-xl font-serif text-gold mb-6">Order Timeline</h3>
                    <div class="timeline">
                        <div class="timeline-item">
                            <h4 class="text-gold font-semibold">Order Confirmed</h4>
                            <p class="text-gray-300 text-sm">Your order has been received and confirmed</p>
                            <p class="text-gray-500 text-xs"><?= date('M d, Y H:i') ?></p>
                        </div>
                        
                        <div class="timeline-item">
                            <h4 class="text-gold font-semibold">Processing</h4>
                            <p class="text-gray-300 text-sm">We're preparing your order for shipment</p>
                            <p class="text-gray-500 text-xs">Within 24 hours</p>
                        </div>
                        
                        <div class="timeline-item">
                            <h4 class="text-gold font-semibold">Shipped</h4>
                            <p class="text-gray-300 text-sm">Your order will be shipped with tracking</p>
                            <p class="text-gray-500 text-xs">1-2 business days</p>
                        </div>
                        
                        <div class="timeline-item">
                            <h4 class="text-gold font-semibold">Delivered</h4>
                            <p class="text-gray-300 text-sm">Your luxury timepiece arrives at your doorstep</p>
                            <p class="text-gray-500 text-xs">3-5 business days</p>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="next-steps">
                    <h3 class="text-xl font-serif text-gold mb-4">What Happens Next?</h3>
                    
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div>
                            <h4 class="text-gold font-semibold">Order Processing</h4>
                            <p class="text-gray-300 text-sm">We'll verify your order and prepare it for shipment within 24 hours</p>
                        </div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div>
                            <h4 class="text-gold font-semibold">Shipping Confirmation</h4>
                            <p class="text-gray-300 text-sm">You'll receive a shipping confirmation email with tracking details</p>
                        </div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div>
                            <h4 class="text-gold font-semibold">Secure Delivery</h4>
                            <p class="text-gray-300 text-sm">Your package will be delivered with signature confirmation</p>
                        </div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">4</div>
                        <div>
                            <h4 class="text-gold font-semibold">Enjoy Your Timepiece</h4>
                            <p class="text-gray-300 text-sm">Unbox your luxury watch and start enjoying it</p>
                        </div>
                    </div>
                </div>

                <!-- Customer Support -->
                <div class="glass p-8 mt-12 text-center">
                    <h3 class="text-xl font-serif text-gold mb-4">Need Help?</h3>
                    <p class="text-gray-300 mb-6">
                        Our customer support team is here to help with any questions about your order.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="<?= CONTACT_PAGE ?>" class="bg-gold text-black px-6 py-2 rounded-full font-semibold hover:bg-white hover:text-gold transition-colors duration-300">
                            Contact Support
                        </a>
                        <a href="tel:+918160375699" class="border border-gold text-gold px-6 py-2 rounded-full font-semibold hover:bg-gold hover:text-black transition-colors duration-300">
                            Call Us
                        </a>
                    </div>
                </div>

                <!-- Additional Services -->
                <div class="mt-12">
                    <h3 class="text-xl font-serif text-gold mb-6 text-center">Additional Services</h3>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="glass p-6 text-center">
                            <i class="fas fa-tools text-3xl text-gold mb-4"></i>
                            <h4 class="text-gold font-semibold mb-2">Servicing</h4>
                            <p class="text-gray-300 text-sm">Professional watch servicing and maintenance</p>
                        </div>
                        
                        <div class="glass p-6 text-center">
                            <i class="fas fa-shield-alt text-3xl text-gold mb-4"></i>
                            <h4 class="text-gold font-semibold mb-2">Extended Warranty</h4>
                            <p class="text-gray-300 text-sm">Additional protection for your investment</p>
                        </div>
                        
                        <div class="glass p-6 text-center">
                            <i class="fas fa-gift text-3xl text-gold mb-4"></i>
                            <h4 class="text-gold font-semibold mb-2">Gift Wrapping</h4>
                            <p class="text-gray-300 text-sm">Premium gift wrapping service available</p>
                        </div>
                    </div>
                </div>

                <!-- Important Information -->
                <div class="mt-8 p-6 bg-gray-900 rounded-lg">
                    <h4 class="text-gold font-semibold mb-3">Important Information:</h4>
                    <ul class="text-gray-300 text-sm space-y-2">
                        <li>• Keep your order number safe for tracking and support</li>
                        <li>• Delivery requires signature confirmation</li>
                        <li>• Returns accepted within 30 days of delivery</li>
                        <li>• All watches come with manufacturer warranty</li>
                        <li>• Contact us immediately if you notice any issues</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions -->
    <section class="py-20 bg-[#0c0c0c]">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="glass p-6 text-center">
                    <i class="fas fa-envelope text-3xl text-gold mb-4"></i>
                    <h3 class="text-lg font-serif text-gold mb-2">Email Confirmation</h3>
                    <p class="text-gray-300 text-sm">Check your inbox</p>
                </div>
                
                <div class="glass p-6 text-center">
                    <i class="fas fa-truck text-3xl text-gold mb-4"></i>
                    <h3 class="text-lg font-serif text-gold mb-2">Free Shipping</h3>
                    <p class="text-gray-300 text-sm">Premium delivery service</p>
                </div>
                
                <div class="glass p-6 text-center">
                    <i class="fas fa-shield-alt text-3xl text-gold mb-4"></i>
                    <h3 class="text-lg font-serif text-gold mb-2">Secure Payment</h3>
                    <p class="text-gray-300 text-sm">100% secure transaction</p>
                </div>
                
                <div class="glass p-6 text-center">
                    <i class="fas fa-headset text-3xl text-gold mb-4"></i>
                    <h3 class="text-lg font-serif text-gold mb-2">24/7 Support</h3>
                    <p class="text-gray-300 text-sm">Always here to help</p>
                </div>
            </div>
        </div>
    </section>

    <?php require_once __DIR__ . '/../templates/footer.php'; ?>

    <script>
        // Animate elements on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (window.anime) {
                // Hero animation
                anime({
                    targets: '.confirmation-hero h1, .confirmation-hero p',
                    opacity: [0, 1],
                    translateY: [30, 0],
                    delay: anime.stagger(200),
                    duration: 1000,
                    easing: 'easeOutCubic'
                });
                
                // Confirmation card animation
                anime({
                    targets: '.confirmation-card',
                    opacity: [0, 1],
                    scale: [0.9, 1],
                    duration: 800,
                    easing: 'easeOutBack'
                });
                
                // Timeline animation
                anime({
                    targets: '.timeline-item',
                    opacity: [0, 1],
                    translateX: [-30, 0],
                    delay: anime.stagger(300),
                    duration: 600,
                    easing: 'easeOutCubic'
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
        
        // Clear cart after successful order
        if (typeof localStorage !== 'undefined') {
            localStorage.removeItem('cart');
        }
        
        // Auto-redirect to account page after 10 seconds
        setTimeout(function() {
            if (confirm('Would you like to view your order details in your account?')) {
                window.location.href = '<?= ACCOUNT_PAGE ?>';
            }
        }, 10000);
    </script>
</body>
</html> 