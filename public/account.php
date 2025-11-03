<?php
session_start();
require_once __DIR__ . '/../templates/header.php';
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
require_once __DIR__ . '/../core/db/connection.php';
$user_id = $_SESSION['user']['id'];
$result = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
?>
<section class="container mx-auto px-6 py-16 text-white">
  <h1 class="text-3xl font-serif text-gold mb-6">My Account</h1>
  <div class="grid md:grid-cols-3 gap-10">
    <!-- Sidebar -->
    <aside class="bg-[#0e0e0e] p-6 rounded-lg">
      <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['user']['name']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
      <a href="/logout.php" class="text-red-400 mt-4 block">Logout</a>
    </aside>
    <!-- Orders -->
    <div class="md:col-span-2">
      <h2 class="text-lg mb-4">Your Orders</h2>
      <?php while ($order = mysqli_fetch_assoc($result)): ?>
        <div class="bg-[#111] p-4 mb-4 rounded-lg product-card">
          <h3 class="font-semibold">Order #<?= $order['id'] ?></h3>
          <p class="text-sm text-gray-400">₹<?= number_format($order['total']) ?> on <?= $order['created_at'] ?></p>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<script src="../assets/js/ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script>
if (window.anime) {
  anime({
    targets: '.product-card',
    opacity: [0,1],
    translateY: [40,0],
    delay: anime.stagger(80),
    duration: 700,
    easing: 'easeOutCubic'
  });
}
</script>
<?php require_once __DIR__ . '/../templates/footer.php'; ?> 