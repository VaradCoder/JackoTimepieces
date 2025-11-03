<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $watchId => $qty) {
        $watchId = intval($watchId);
        $qty = intval($qty);
        if ($qty < 1) {
            unset($_SESSION['cart'][$watchId]);
        } else {
            $_SESSION['cart'][$watchId] = $qty;
        }
    }
}
header('Location: cart.php');
exit; 