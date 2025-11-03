<?php
session_start();
require_once '../../core/db/connection.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user_id = $_SESSION['user']['id'];
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
if ($name && $email) {
    $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
    $stmt->bind_param('ssi', $name, $email, $user_id);
    $stmt->execute();
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['email'] = $email;
}
header('Location: settings.php');
exit; 