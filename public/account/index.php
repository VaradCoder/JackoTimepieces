<?php
session_start();
require_once '../../core/config/constants.php';
if (!isset($_SESSION['user'])) header('Location: ' . LOGIN_PAGE);
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Dashboard | JackoTimespiece</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
</head>
<body class="bg-black text-white font-sans min-h-screen">
  <?php require_once '../../templates/header.php'; ?>
  <section class="container mx-auto px-6 py-16 text-white">
    <h1 class="text-3xl font-serif text-gold mb-6">Welcome, <?= htmlspecialchars($user['name']) ?></h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <a href="orders.php" class="bg-[#111] p-4 rounded shadow hover:bg-[#222] transition">📦 My Orders</a>
      <a href="wishlist.php" class="bg-[#111] p-4 rounded shadow hover:bg-[#222] transition">❤️ Wishlist</a>
      <a href="settings.php" class="bg-[#111] p-4 rounded shadow hover:bg-[#222] transition">⚙️ Account Settings</a>
      <a href="addresses.php" class="bg-[#111] p-4 rounded shadow hover:bg-[#222] transition">📍 Addresses</a>
    </div>
  </section>
  <?php require_once '../../templates/footer.php'; ?>
  <script>
    if (window.anime) {
      anime({
        targets: 'a.bg-[#111]',
        opacity: [0,1],
        translateY: [40,0],
        delay: anime.stagger(100),
        duration: 700,
        easing: 'easeOutCubic'
      });
    }
  </script>
</body>
</html> 