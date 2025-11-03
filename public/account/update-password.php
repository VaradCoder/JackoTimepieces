<?php
session_start();
require_once '../../core/db/connection.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user_id = $_SESSION['user']['id'];
$old = $_POST['old_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';
if ($new && $new === $confirm) {
    $res = $conn->query("SELECT password FROM users WHERE id = $user_id");
    $row = $res->fetch_assoc();
    if (password_verify($old, $row['password'])) {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$hash' WHERE id=$user_id");
    }
}
header('Location: settings.php');
exit; 