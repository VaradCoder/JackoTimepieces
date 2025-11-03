<?php
session_start();

// Check if cart is empty - do this BEFORE including header template
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: ../public/cart.php');
    exit;
}

require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../core/db/connection.php';

// Calculate totals
        $total = 0;
$cart_items = [];
        foreach ($_SESSION['cart'] as $id => $qty) {
          $res = mysqli_query($conn, "SELECT * FROM watches WHERE id = $id");
          $watch = mysqli_fetch_assoc($res);
          $subtotal = $watch['price'] * $qty;
          $total += $subtotal;
    $cart_items[] = [
        'id' => $id,
        'name' => $watch['name'],
        'price' => $watch['price'],
        'qty' => $qty,
        'subtotal' => $subtotal,
        'image' => $watch['image']
    ];
}

$shipping = 0; // Free shipping
$tax = $total * 0.18; // 18% GST
$final_total = $total + $shipping + $tax;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | JackoTimespiece</title>
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
        
        .checkout-container {
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
        
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .form-input {
            width: 100%;
            padding: 1rem 1.5rem;
            background: rgba(12, 12, 12, 0.8);
            border: 2px solid rgba(201, 179, 126, 0.3);
            border-radius: 0.75rem;
            color: white;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            outline: none;
        }
        
        .form-input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(201, 179, 126, 0.1);
            background: rgba(12, 12, 12, 0.9);
        }
        
        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .form-label {
            position: absolute;
            top: -0.5rem;
            left: 1rem;
            background: var(--dark-gray);
            padding: 0 0.5rem;
            color: var(--gold);
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.25rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: var(--black);
            padding: 1rem 2rem;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(201, 179, 126, 0.3);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .cart-item {
            background: rgba(12, 12, 12, 0.6);
            border: 1px solid rgba(201, 179, 126, 0.2);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            border-color: var(--gold);
            background: rgba(12, 12, 12, 0.8);
        }
        
        .price-breakdown {
            background: rgba(12, 12, 12, 0.4);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(201, 179, 126, 0.1);
        }
        
        .price-row:last-child {
            border-bottom: none;
            font-weight: 600;
            font-size: 1.125rem;
            color: var(--gold);
        }
        
        .section-title {
            font-family: 'Playfair Display', serif;
            color: var(--gold);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -0.5rem;
            left: 0;
            width: 3rem;
            height: 2px;
            background: var(--gold);
        }
        
        .floating-label {
            position: absolute;
            top: 1rem;
            left: 1.5rem;
            color: rgba(255, 255, 255, 0.6);
            transition: all 0.3s ease;
            pointer-events: none;
        }
        
        .form-input:focus + .floating-label,
        .form-input:not(:placeholder-shown) + .floating-label {
            top: -0.5rem;
            left: 1rem;
            font-size: 0.75rem;
            color: var(--gold);
            background: var(--dark-gray);
            padding: 0 0.5rem;
            border-radius: 0.25rem;
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
    <div class="checkout-container py-16">
        <div class="container mx-auto px-6">
            <!-- Header -->
            <div class="text-center mb-12 animate-fade-in-up">
                <h1 class="text-4xl md:text-5xl font-serif text-gold mb-4">Complete Your Order</h1>
                <p class="text-gray-400 text-lg">Secure checkout for your luxury timepiece</p>
            </div>
            
            <form method="POST" action="../public/payment.php" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left: Shipping Information -->
                <div class="space-y-6">
                    <div class="glass-card p-8 animate-fade-in-up animate-delay-1">
                        <h2 class="section-title">Shipping Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <input type="text" name="first_name" id="first_name" class="form-input" placeholder=" " required>
                                <label for="first_name" class="floating-label">First Name</label>
                            </div>
                            
                            <div class="form-group">
                                <input type="text" name="last_name" id="last_name" class="form-input" placeholder=" " required>
                                <label for="last_name" class="floating-label">Last Name</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <input type="email" name="email" id="email" class="form-input" placeholder=" " required>
                            <label for="email" class="floating-label">Email Address</label>
                        </div>
                        
                        <div class="form-group">
                            <input type="tel" name="phone" id="phone" class="form-input" placeholder=" " required>
                            <label for="phone" class="floating-label">Phone Number</label>
                        </div>
                        
                        <div class="form-group">
                            <input type="text" name="address" id="address" class="form-input" placeholder=" " required>
                            <label for="address" class="floating-label">Street Address</label>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="form-group">
                                <input type="text" name="city" id="city" class="form-input" placeholder=" " required>
                                <label for="city" class="floating-label">City</label>
                            </div>
                            
                            <div class="form-group">
                                <input type="text" name="state" id="state" class="form-input" placeholder=" " required>
                                <label for="state" class="floating-label">State</label>
                            </div>
                            
                            <div class="form-group">
                                <input type="text" name="zip" id="zip" class="form-input" placeholder=" " required>
                                <label for="zip" class="floating-label">ZIP Code</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Information -->
                    <div class="glass-card p-8 animate-fade-in-up animate-delay-2">
                        <h2 class="section-title">Additional Information</h2>
                        
                        <div class="form-group">
                            <textarea name="notes" id="notes" class="form-input" rows="4" placeholder=" "></textarea>
                            <label for="notes" class="floating-label">Order Notes (Optional)</label>
                        </div>
                        
                        <div class="form-group">
                            <select name="shipping_method" id="shipping_method" class="form-input" required>
                                <option value="">Select Shipping Method</option>
                                <option value="standard">Standard Delivery (3-5 days) - Free</option>
                                <option value="express">Express Delivery (1-2 days) - ₹500</option>
                                <option value="premium">Premium Delivery (Same day) - ₹1000</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Order Summary -->
                <div class="space-y-6">
                    <div class="glass-card p-8 animate-fade-in-up animate-delay-3">
                        <h2 class="section-title">Order Summary</h2>
                        
                        <!-- Cart Items -->
                        <div class="space-y-4 mb-6">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item">
                                <div class="flex items-center space-x-4">
                                    <img src="../assets/images/watches/<?= htmlspecialchars($item['image']) ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>" 
                                         class="w-16 h-16 object-cover rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-white"><?= htmlspecialchars($item['name']) ?></h4>
                                        <p class="text-gray-400 text-sm">Quantity: <?= $item['qty'] ?></p>
                                        <p class="text-gold font-semibold">₹<?= number_format($item['subtotal']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Price Breakdown -->
                        <div class="price-breakdown">
                            <div class="price-row">
                                <span>Subtotal</span>
                                <span>₹<?= number_format($total) ?></span>
                            </div>
                            <div class="price-row">
                                <span>Shipping</span>
                                <span class="text-green-400">Free</span>
                            </div>
                            <div class="price-row">
                                <span>Tax (18% GST)</span>
                                <span>₹<?= number_format($tax) ?></span>
                            </div>
                            <div class="price-row">
                                <span class="text-lg font-bold">Total</span>
                                <span class="text-lg font-bold text-gold">₹<?= number_format($final_total) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Security & Trust -->
                    <div class="glass-card p-6 animate-fade-in-up animate-delay-4">
                        <div class="flex items-center justify-center space-x-6 text-gray-400">
                            <div class="text-center">
                                <i class="fas fa-shield-alt text-2xl text-gold mb-2"></i>
                                <p class="text-xs">SSL Secured</p>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-lock text-2xl text-gold mb-2"></i>
                                <p class="text-xs">Secure Payment</p>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-undo text-2xl text-gold mb-2"></i>
                                <p class="text-xs">Easy Returns</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden fields for payment page -->
                    <input type="hidden" name="total" value="<?= $final_total ?>">
                    <input type="hidden" name="subtotal" value="<?= $total ?>">
                    <input type="hidden" name="tax" value="<?= $tax ?>">
                    <input type="hidden" name="shipping" value="<?= $shipping ?>">
                    <?php foreach ($cart_items as $item): ?>
                        <input type="hidden" name="cart_items[]" value="<?= $item['id'] ?>:<?= $item['qty'] ?>">
                    <?php endforeach; ?>
                    
                    <!-- Proceed to Payment Button -->
                    <button type="submit" class="btn-primary w-full py-4 text-lg animate-fade-in-up animate-delay-5">
                        <i class="fas fa-credit-card mr-2"></i>
                        Proceed to Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
        // Form validation and animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate form elements on scroll
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

            // Observe all form elements
            document.querySelectorAll('.form-group, .cart-item, .price-breakdown').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.6s ease-out';
                observer.observe(el);
            });

            // Form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.style.borderColor = '#ef4444';
                    } else {
                        field.style.borderColor = 'rgba(201, 179, 126, 0.3)';
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                }
            });

            // Phone number formatting
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 10) value = value.slice(0, 10);
                e.target.value = value.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
            });

            // ZIP code formatting
            const zipInput = document.getElementById('zip');
            zipInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 6) value = value.slice(0, 6);
                e.target.value = value;
            });
        });
</script>
</body>
</html>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 

