<?php
// Database connection for JackoTimespiece
$host = 'localhost';
$user = 'root';         // Default XAMPP user
$pass = '';             // Default XAMPP password is empty
$db   = 'jackotimespiece';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
?>