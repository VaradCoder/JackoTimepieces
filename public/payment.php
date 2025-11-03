<?php
session_start();

// Check if form data is submitted - do this BEFORE including header template
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/checkout.php');
    exit;
}

require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../core/db/connection.php';

// Get form data
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$city = $_POST['city'] ?? '';
$state = $_POST['state'] ?? '';
$zip = $_POST['zip'] ?? '';
$notes = $_POST['notes'] ?? '';
$shipping_method = $_POST['shipping_method'] ?? 'standard';

$total = $_POST['total'] ?? 0;
$subtotal = $_POST['subtotal'] ?? 0;
$tax = $_POST['tax'] ?? 0;
$shipping = $_POST['shipping'] ?? 0;

// Store order data in session for processing
$_SESSION['order_data'] = [
    'customer_info' => [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'city' => $city,
        'state' => $state,
        'zip' => $zip,
        'notes' => $notes,
        'shipping_method' => $shipping_method
    ],
    'payment_info' => [
        'total' => $total,
        'subtotal' => $subtotal,
        'tax' => $tax,
        'shipping' => $shipping
    ],
    'cart_items' => $_POST['cart_items'] ?? []
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment | JackoTimespiece</title>
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
        
        .payment-container {
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
        
        .payment-method {
            background: rgba(12, 12, 12, 0.6);
            border: 2px solid rgba(201, 179, 126, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .payment-method:hover {
            border-color: var(--gold);
            background: rgba(12, 12, 12, 0.8);
            transform: translateY(-2px);
        }
        
        .payment-method.selected {
            border-color: var(--gold);
            background: rgba(201, 179, 126, 0.1);
            box-shadow: 0 0 20px rgba(201, 179, 126, 0.2);
        }
        
        .payment-method input[type="radio"] {
            display: none;
        }
        
        .payment-method .checkmark {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(201, 179, 126, 0.3);
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .payment-method.selected .checkmark {
            background: var(--gold);
            border-color: var(--gold);
        }
        
        .payment-method.selected .checkmark::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: var(--black);
            font-size: 12px;
            font-weight: bold;
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
        
        .btn-secondary {
            background: rgba(12, 12, 12, 0.8);
            color: var(--gold);
            border: 2px solid var(--gold);
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .btn-secondary:hover {
            background: var(--gold);
            color: var(--black);
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
        
        .payment-icon {
            width: 60px;
            height: 60px;
            background: var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--black);
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .upi-qr {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            text-align: center;
            margin-top: 1rem;
        }
        
        .upi-qr img {
            width: 150px;
            height: 150px;
            margin: 0 auto 1rem;
        }
        
        .card-details {
            display: none;
        }
        
        .card-details.active {
            display: block;
        }
        
        .upi-details {
            display: none;
        }
        
        .upi-details.active {
            display: block;
        }
        
        .cash-details {
            display: none;
        }
        
        .cash-details.active {
            display: block;
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
        
        .security-badges {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .security-badge {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .security-badge i {
            font-size: 2rem;
            color: var(--gold);
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body class="bg-black text-white font-sans">
    <div class="payment-container py-16">
        <div class="container mx-auto px-6">
            <!-- Header -->
            <div class="text-center mb-12 animate-fade-in-up">
                <h1 class="text-4xl md:text-5xl font-serif text-gold mb-4">Secure Payment</h1>
                <p class="text-gray-400 text-lg">Choose your preferred payment method</p>
            </div>
            
            <form method="POST" action="../public/process-payment.php" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left: Payment Methods -->
                <div class="space-y-6">
                    <div class="glass-card p-8 animate-fade-in-up animate-delay-1">
                        <h2 class="section-title">Payment Method</h2>
                        
                        <!-- Credit/Debit Card -->
                        <div class="payment-method mb-4" onclick="selectPayment('card')">
                            <input type="radio" name="payment_method" value="card" id="card">
                            <div class="checkmark"></div>
                            <div class="flex items-center space-x-4">
                                <div class="payment-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-white">Credit/Debit Card</h3>
                                    <p class="text-gray-400 text-sm">Visa, Mastercard, American Express</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- UPI -->
                        <div class="payment-method mb-4" onclick="selectPayment('upi')">
                            <input type="radio" name="payment_method" value="upi" id="upi">
                            <div class="checkmark"></div>
                            <div class="flex items-center space-x-4">
                                <div class="payment-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-white">UPI Payment</h3>
                                    <p class="text-gray-400 text-sm">Google Pay, PhonePe, Paytm, BHIM</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cash on Delivery -->
                        <div class="payment-method mb-4" onclick="selectPayment('cash')">
                            <input type="radio" name="payment_method" value="cash" id="cash">
                            <div class="checkmark"></div>
                            <div class="flex items-center space-x-4">
                                <div class="payment-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-white">Cash on Delivery</h3>
                                    <p class="text-gray-400 text-sm">Pay when you receive your order</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Details -->
                        <div id="payment-details" class="mt-8">
                            <!-- Card Details -->
                            <div id="card-details" class="card-details">
                                <h3 class="text-lg font-semibold text-gold mb-4">Card Details</h3>
                                <div class="space-y-4">
                                    <input type="text" name="card_number" placeholder="Card Number" class="form-input" maxlength="19">
                                    <div class="grid grid-cols-2 gap-4">
                                        <input type="text" name="expiry" placeholder="MM/YY" class="form-input" maxlength="5">
                                        <input type="text" name="cvv" placeholder="CVV" class="form-input" maxlength="4">
                                    </div>
                                    <input type="text" name="card_name" placeholder="Name on Card" class="form-input">
                                </div>
                            </div>
                            
                            <!-- UPI Details -->
                            <div id="upi-details" class="upi-details">
                                <h3 class="text-lg font-semibold text-gold mb-4">UPI Payment</h3>
                                <div class="space-y-4">
                                    <input type="text" name="upi_id" placeholder="UPI ID (e.g., name@upi)" class="form-input">
                                    <div class="upi-qr">
                                        <img src="../assets/images/qr-code.png" alt="UPI QR Code" onerror="this.style.display='none'">
                                        <p class="text-black text-sm">Scan QR code or enter UPI ID above</p>
                                        <p class="text-black text-xs mt-2">jackotimespiece@okicici</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Cash Details -->
                            <div id="cash-details" class="cash-details">
                                <h3 class="text-lg font-semibold text-gold mb-4">Cash on Delivery</h3>
                                <div class="bg-green-900 bg-opacity-20 border border-green-500 rounded-lg p-4">
                                    <p class="text-green-400 text-sm">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        You can pay with cash when your order is delivered. 
                                        Please keep the exact amount ready.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Security Information -->
                    <div class="glass-card p-6 animate-fade-in-up animate-delay-2">
                        <div class="security-badges">
                            <div class="security-badge">
                                <i class="fas fa-shield-alt"></i>
                                <p class="text-xs">SSL Secured</p>
                            </div>
                            <div class="security-badge">
                                <i class="fas fa-lock"></i>
                                <p class="text-xs">PCI Compliant</p>
                            </div>
                            <div class="security-badge">
                                <i class="fas fa-user-shield"></i>
                                <p class="text-xs">Data Protected</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Order Summary -->
                <div class="space-y-6">
                    <div class="glass-card p-8 animate-fade-in-up animate-delay-3">
                        <h2 class="section-title">Order Summary</h2>
                        
                        <!-- Customer Info -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-white mb-3">Shipping To:</h3>
                            <div class="bg-gray-900 bg-opacity-50 rounded-lg p-4">
                                <p class="text-white"><?= htmlspecialchars($first_name . ' ' . $last_name) ?></p>
                                <p class="text-gray-400 text-sm"><?= htmlspecialchars($address) ?></p>
                                <p class="text-gray-400 text-sm"><?= htmlspecialchars($city . ', ' . $state . ' ' . $zip) ?></p>
                                <p class="text-gray-400 text-sm"><?= htmlspecialchars($phone) ?></p>
                            </div>
                        </div>
                        
                        <!-- Price Breakdown -->
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Subtotal:</span>
                                <span class="text-white">₹<?= number_format($subtotal) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Shipping:</span>
                                <span class="text-green-400">Free</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Tax (18% GST):</span>
                                <span class="text-white">₹<?= number_format($tax) ?></span>
                            </div>
                            <hr class="border-gray-700">
                            <div class="flex justify-between text-lg font-bold">
                                <span class="text-gold">Total:</span>
                                <span class="text-gold">₹<?= number_format($total) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Buttons -->
                    <div class="space-y-4 animate-fade-in-up animate-delay-4">
                        <button type="submit" id="pay-button" class="btn-primary w-full py-4 text-lg" disabled>
                            <i class="fas fa-lock mr-2"></i>
                            Pay ₹<?= number_format($total) ?>
                        </button>
                        
                        <a href="../public/checkout.php" class="btn-secondary w-full py-4 text-lg text-center block">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Checkout
                        </a>
                    </div>
                    
                    <!-- Terms -->
                    <div class="text-center text-gray-400 text-sm animate-fade-in-up animate-delay-5">
                        <p>By clicking "Pay", you agree to our <a href="../public/pages/terms.php" class="text-gold hover:underline">Terms of Service</a> and <a href="../public/pages/privacy.php" class="text-gold hover:underline">Privacy Policy</a></p>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let selectedPayment = '';
        
        function selectPayment(method) {
            // Remove previous selection
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Hide all payment details
            document.querySelectorAll('.card-details, .upi-details, .cash-details').forEach(el => {
                el.classList.remove('active');
            });
            
            // Select new method
            document.getElementById(method).checked = true;
            document.querySelector(`[onclick="selectPayment('${method}')"]`).classList.add('selected');
            
            // Show relevant details
            if (method === 'card') {
                document.getElementById('card-details').classList.add('active');
            } else if (method === 'upi') {
                document.getElementById('upi-details').classList.add('active');
            } else if (method === 'cash') {
                document.getElementById('cash-details').classList.add('active');
            }
            
            selectedPayment = method;
            updatePayButton();
        }
        
        function updatePayButton() {
            const payButton = document.getElementById('pay-button');
            if (selectedPayment) {
                payButton.disabled = false;
                payButton.style.opacity = '1';
                payButton.style.cursor = 'pointer';
            } else {
                payButton.disabled = true;
                payButton.style.opacity = '0.5';
                payButton.style.cursor = 'not-allowed';
            }
        }
        
        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            
            // Card number formatting
            const cardInput = document.querySelector('input[name="card_number"]');
            if (cardInput) {
                cardInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 16) value = value.slice(0, 16);
                    e.target.value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
                });
            }
            
            // Expiry date formatting
            const expiryInput = document.querySelector('input[name="expiry"]');
            if (expiryInput) {
                expiryInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 4) value = value.slice(0, 4);
                    if (value.length >= 2) {
                        value = value.slice(0, 2) + '/' + value.slice(2);
                    }
                    e.target.value = value;
                });
            }
            
            // CVV formatting
            const cvvInput = document.querySelector('input[name="cvv"]');
            if (cvvInput) {
                cvvInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 4) value = value.slice(0, 4);
                    e.target.value = value;
                });
            }
            
            // Form submission
            form.addEventListener('submit', function(e) {
                if (!selectedPayment) {
                    e.preventDefault();
                    alert('Please select a payment method.');
                    return;
                }
                
                if (selectedPayment === 'card') {
                    const cardNumber = document.querySelector('input[name="card_number"]').value;
                    const expiry = document.querySelector('input[name="expiry"]').value;
                    const cvv = document.querySelector('input[name="cvv"]').value;
                    const cardName = document.querySelector('input[name="card_name"]').value;
                    
                    if (!cardNumber || !expiry || !cvv || !cardName) {
                        e.preventDefault();
                        alert('Please fill in all card details.');
                        return;
                    }
                } else if (selectedPayment === 'upi') {
                    const upiId = document.querySelector('input[name="upi_id"]').value;
                    if (!upiId) {
                        e.preventDefault();
                        alert('Please enter your UPI ID.');
                        return;
                    }
                }
                
                // Show loading state
                const payButton = document.getElementById('pay-button');
                payButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                payButton.disabled = true;
            });
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

        document.querySelectorAll('.payment-method, .glass-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease-out';
            observer.observe(el);
        });
    </script>
</body>
</html>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 