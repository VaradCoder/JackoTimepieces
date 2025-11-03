<?php
session_start();
require_once '../../core/db/connection.php';
if (!isset($_SESSION['user'])) header('Location: ../login.php');
$user_id = $_SESSION['user']['id'];
if (isset($_GET['id'])) {
    $watch_id = intval($_GET['id']);
    $conn->query("DELETE FROM wishlist WHERE user_id = $user_id AND watch_id = $watch_id");
}
header('Location: wishlist.php');
exit; 