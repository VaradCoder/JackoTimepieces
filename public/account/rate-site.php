<?php
session_start();
require_once '../../core/db/connection.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user_id = $_SESSION['user']['id'];
$rating = intval($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');
if ($rating > 0) {
    $stmt = $conn->prepare("INSERT INTO site_ratings (user_id, rating, comment) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $user_id, $rating, $comment);
    $stmt->execute();
}
header('Location: settings.php');
exit; 