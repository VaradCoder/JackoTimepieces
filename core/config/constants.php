<?php // Constants config 
// TODO: Add constants

define('BASE_PATH', '/Watch');

// Use relative paths for consistent navigation
define('INDEX_PAGE', BASE_PATH . '/public/index.php');
define('STORE_PAGE', BASE_PATH . '/public/store.php');
define('WATCH_PAGE', BASE_PATH . '/public/watch.php');
define('MEN_PAGE', BASE_PATH . '/public/men.php');
define('WOMEN_PAGE', BASE_PATH . '/public/women.php');
define('UNISEX_PAGE', BASE_PATH . '/public/unisex.php');
define('CART_PAGE', BASE_PATH . '/public/cart.php');
define('CHECKOUT_PAGE', BASE_PATH . '/public/checkout.php');
define('CONFIRMATION_PAGE', BASE_PATH . '/public/confirmation.php');
define('LOGIN_PAGE', BASE_PATH . '/public/login.php');
define('REGISTER_PAGE', BASE_PATH . '/public/register.php');
define('ACCOUNT_INDEX', BASE_PATH . '/public/account/index.php');
define('ACCOUNT_ORDERS', BASE_PATH . '/public/account/orders.php');
define('ACCOUNT_WISHLIST', BASE_PATH . '/public/account/wishlist.php');
define('ACCOUNT_SETTINGS', BASE_PATH . '/public/account/settings.php');
define('ACCOUNT_ADDRESSES', BASE_PATH . '/public/account/addresses.php');
define('ABOUT_PAGE', BASE_PATH . '/public/pages/about.php');
define('CONTACT_PAGE', BASE_PATH . '/public/pages/contact.php');
define('FAQ_PAGE', BASE_PATH . '/public/pages/faq.php');
define('PRIVACY_PAGE', BASE_PATH . '/public/pages/privacy.php');
define('TERMS_PAGE', BASE_PATH . '/public/pages/terms.php');
define('RETURN_POLICY_PAGE', BASE_PATH . '/public/pages/return-policy.php');
define('OFFERS_CURRENT', BASE_PATH . '/public/offers/current.php');
define('OFFERS_COUPON_SUCCESS', BASE_PATH . '/public/offers/coupon-success.php');
define('INSTA', 'www.instagram.com/jackotimespieces');
?>

<?php
// Determine the base path for assets
$current_path = $_SERVER['PHP_SELF'];
$is_in_pages = strpos($current_path, '/pages/') !== false;
$assets_base = $is_in_pages ? '../' : '';
?>