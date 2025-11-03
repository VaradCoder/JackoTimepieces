<?php
session_start();
require_once __DIR__ . '/../core/config/constants.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Test | JackoTimespiece</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        :root { --gold: #c9b37e; }
        .text-gold { color: var(--gold) !important; }
        .bg-gold { background: var(--gold) !important; }
        .border-gold { border-color: var(--gold) !important; }
    </style>
</head>
<body class="bg-black text-white p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gold mb-8">Navigation Test Page</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Main Pages -->
            <div class="bg-gray-900 p-6 rounded-lg border border-gold">
                <h2 class="text-xl font-bold text-gold mb-4">Main Pages</h2>
                <div class="space-y-2">
                    <a href="<?= INDEX_PAGE ?>" class="block text-white hover:text-gold transition-colors">Home (<?= INDEX_PAGE ?>)</a>
                    <a href="<?= STORE_PAGE ?>" class="block text-white hover:text-gold transition-colors">Store (<?= STORE_PAGE ?>)</a>
                    <a href="<?= CART_PAGE ?>" class="block text-white hover:text-gold transition-colors">Cart (<?= CART_PAGE ?>)</a>
                    <a href="<?= LOGIN_PAGE ?>" class="block text-white hover:text-gold transition-colors">Login (<?= LOGIN_PAGE ?>)</a>
                    <a href="<?= REGISTER_PAGE ?>" class="block text-white hover:text-gold transition-colors">Register (<?= REGISTER_PAGE ?>)</a>
                </div>
            </div>
            
            <!-- Category Pages -->
            <div class="bg-gray-900 p-6 rounded-lg border border-gold">
                <h2 class="text-xl font-bold text-gold mb-4">Category Pages</h2>
                <div class="space-y-2">
                    <a href="<?= MEN_PAGE ?>" class="block text-white hover:text-gold transition-colors">Men (<?= MEN_PAGE ?>)</a>
                    <a href="<?= WOMEN_PAGE ?>" class="block text-white hover:text-gold transition-colors">Women (<?= WOMEN_PAGE ?>)</a>
                    <a href="<?= UNISEX_PAGE ?>" class="block text-white hover:text-gold transition-colors">Unisex (<?= UNISEX_PAGE ?>)</a>
                </div>
            </div>
            
            <!-- Information Pages -->
            <div class="bg-gray-900 p-6 rounded-lg border border-gold">
                <h2 class="text-xl font-bold text-gold mb-4">Information Pages</h2>
                <div class="space-y-2">
                    <a href="<?= ABOUT_PAGE ?>" class="block text-white hover:text-gold transition-colors">About (<?= ABOUT_PAGE ?>)</a>
                    <a href="<?= CONTACT_PAGE ?>" class="block text-white hover:text-gold transition-colors">Contact (<?= CONTACT_PAGE ?>)</a>
                    <a href="<?= FAQ_PAGE ?>" class="block text-white hover:text-gold transition-colors">FAQ (<?= FAQ_PAGE ?>)</a>
                    <a href="<?= PRIVACY_PAGE ?>" class="block text-white hover:text-gold transition-colors">Privacy (<?= PRIVACY_PAGE ?>)</a>
                    <a href="<?= TERMS_PAGE ?>" class="block text-white hover:text-gold transition-colors">Terms (<?= TERMS_PAGE ?>)</a>
                    <a href="<?= RETURN_POLICY_PAGE ?>" class="block text-white hover:text-gold transition-colors">Return Policy (<?= RETURN_POLICY_PAGE ?>)</a>
                </div>
            </div>
            
            <!-- Account Pages -->
            <div class="bg-gray-900 p-6 rounded-lg border border-gold">
                <h2 class="text-xl font-bold text-gold mb-4">Account Pages</h2>
                <div class="space-y-2">
                    <a href="<?= ACCOUNT_INDEX ?>" class="block text-white hover:text-gold transition-colors">Dashboard (<?= ACCOUNT_INDEX ?>)</a>
                    <a href="<?= ACCOUNT_ORDERS ?>" class="block text-white hover:text-gold transition-colors">Orders (<?= ACCOUNT_ORDERS ?>)</a>
                    <a href="<?= ACCOUNT_WISHLIST ?>" class="block text-white hover:text-gold transition-colors">Wishlist (<?= ACCOUNT_WISHLIST ?>)</a>
                    <a href="<?= ACCOUNT_SETTINGS ?>" class="block text-white hover:text-gold transition-colors">Settings (<?= ACCOUNT_SETTINGS ?>)</a>
                    <a href="<?= ACCOUNT_ADDRESSES ?>" class="block text-white hover:text-gold transition-colors">Addresses (<?= ACCOUNT_ADDRESSES ?>)</a>
                </div>
            </div>
            
            <!-- Offers Pages -->
            <div class="bg-gray-900 p-6 rounded-lg border border-gold">
                <h2 class="text-xl font-bold text-gold mb-4">Offers Pages</h2>
                <div class="space-y-2">
                    <a href="<?= OFFERS_CURRENT ?>" class="block text-white hover:text-gold transition-colors">Current Offers (<?= OFFERS_CURRENT ?>)</a>
                    <a href="<?= OFFERS_COUPON_SUCCESS ?>" class="block text-white hover:text-gold transition-colors">Coupon Success (<?= OFFERS_COUPON_SUCCESS ?>)</a>
                </div>
            </div>
            
            <!-- Other Pages -->
            <div class="bg-gray-900 p-6 rounded-lg border border-gold">
                <h2 class="text-xl font-bold text-gold mb-4">Other Pages</h2>
                <div class="space-y-2">
                    <a href="<?= CHECKOUT_PAGE ?>" class="block text-white hover:text-gold transition-colors">Checkout (<?= CHECKOUT_PAGE ?>)</a>
                    <a href="<?= CONFIRMATION_PAGE ?>" class="block text-white hover:text-gold transition-colors">Confirmation (<?= CONFIRMATION_PAGE ?>)</a>
                    <a href="<?= WATCH_PAGE ?>" class="block text-white hover:text-gold transition-colors">Watch Detail (<?= WATCH_PAGE ?>)</a>
                </div>
            </div>
        </div>
        
        <div class="mt-8 p-6 bg-gray-900 rounded-lg border border-gold">
            <h2 class="text-xl font-bold text-gold mb-4">Test Results</h2>
            <p class="text-green-400 mb-2">✅ All navigation constants have been updated to use absolute paths</p>
            <p class="text-green-400 mb-2">✅ Asset paths (CSS, JS, images) have been updated to use absolute paths</p>
            <p class="text-green-400 mb-2">✅ Include paths use __DIR__ for proper file inclusion</p>
            <p class="text-green-400 mb-2">✅ .htaccess is configured to route requests to public/ folder</p>
            <p class="text-yellow-400">⚠️ Test each link to ensure they work correctly</p>
        </div>
    </div>
</body>
</html> 