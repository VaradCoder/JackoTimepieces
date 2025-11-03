<?php
session_start();
require_once '../../core/db/connection.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user_id = $_SESSION['user']['id'];
$message = trim($_POST['message'] ?? '');
if ($message) {
    $stmt = $conn->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
    $stmt->bind_param('is', $user_id, $message);
    $stmt->execute();
}
header('Location: settings.php');
exit; 