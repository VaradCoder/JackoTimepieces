<?php
session_start();
require_once '../../core/db/connection.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user_id = $_SESSION['user']['id'];
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
    $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
    $filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
    $dest = '../../assets/images/users/' . $filename;
    move_uploaded_file($_FILES['profile_pic']['tmp_name'], $dest);
    $conn->query("UPDATE users SET profile_pic='$filename' WHERE id=$user_id");
    $_SESSION['user']['image'] = $filename;
}
header('Location: settings.php');
exit; 