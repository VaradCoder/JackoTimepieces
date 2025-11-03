<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
if (isset($_GET['id'])) {
    $watchId = intval($_GET['id']);
    unset($_SESSION['cart'][$watchId]);
}
header('Location: cart.php');
exit; 