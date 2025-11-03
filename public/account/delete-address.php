<?php
session_start();
require_once '../../core/db/connection.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user_id = $_SESSION['user']['id'];
if (isset($_GET['id'])) {
    $addr_id = intval($_GET['id']);
    $conn->query("DELETE FROM addresses WHERE id = $addr_id AND user_id = $user_id");
}
header('Location: addresses.php');
exit; 