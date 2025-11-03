<?php
session_start();
require_once '../../core/db/connection.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user_id = $_SESSION['user']['id'];
$line1 = trim($_POST['line1'] ?? '');
$city = trim($_POST['city'] ?? '');
$zip = trim($_POST['zip'] ?? '');
$is_primary = isset($_POST['is_primary']) ? 1 : 0;
if ($line1 && $city && $zip) {
    if ($is_primary) $conn->query("UPDATE addresses SET is_primary=0 WHERE user_id=$user_id");
    $stmt = $conn->prepare("INSERT INTO addresses (user_id, line1, city, zip, is_primary) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('isssi', $user_id, $line1, $city, $zip, $is_primary);
    $stmt->execute();
}
header('Location: addresses.php');
exit; 