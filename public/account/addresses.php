<?php
session_start();
require_once '../../core/config/constants.php';
if (!isset($_SESSION['user'])) header('Location: ' . LOGIN_PAGE);
require_once '../../core/db/connection.php';
$user_id = $_SESSION['user']['id'];
$res = mysqli_query($conn, "SELECT * FROM addresses WHERE user_id = $user_id");
if (!$res) {
    die("SQL Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Address Book | JackoTimespiece</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
</head>
<body class="bg-black text-white font-sans min-h-screen">
  <?php require_once '../../templates/header.php'; ?>
  <section class="container mx-auto px-6 py-16 text-white">
    <h2 class="text-2xl font-serif text-gold mb-6">Your Addresses</h2>
    <?php while ($addr = mysqli_fetch_assoc($res)) : ?>
      <div class="bg-[#111] p-4 rounded mb-4 address-card">
        <p><?= htmlspecialchars($addr['line1']) ?>, <?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['zip']) ?></p>
        <?php if ($addr['is_primary']) echo '<span class="text-green-400 text-sm">Primary</span>'; ?>
      </div>
    <?php endwhile; ?>
  </section>
  <?php require_once '../../templates/footer.php'; ?>
  <script>
    if (window.anime) {
      anime({
        targets: '.address-card',
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