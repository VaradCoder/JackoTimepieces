<?php
session_start();
require_once __DIR__ . '/../core/db/connection.php';

// Handle Add to Cart POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['watch_id'])) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login.php');
        exit;
    }
    $watchId = intval($_POST['watch_id']);
    if (!isset($_SESSION['cart'][$watchId])) {
        $_SESSION['cart'][$watchId] = 1;
    } else {
        $_SESSION['cart'][$watchId]++;
    }
    header('Location: /cart.php');
    exit;
}
require_once __DIR__ . '/../templates/header.php';
?>
<section class="container mx-auto px-6 py-16 text-white">
  <h1 class="text-3xl font-serif text-gold mb-6">Your Cart</h1>
  <?php
  if (empty($_SESSION['cart'])) {
    echo "<p class='text-gray-400'>Your cart is empty. <a href='/Watch/public/store.php' class='text-gold underline'>Continue Shopping</a></p>";
  } else {
    $total = 0;
  ?>
  <form method="POST" action="update-cart.php">
    <div class="overflow-x-auto">
      <table class="min-w-full border border-gold rounded-lg">
        <thead>
          <tr class="bg-[#181818] text-gold">
            <th class="py-3 px-4 border-b border-gold">Image</th>
            <th class="py-3 px-4 border-b border-gold">Name</th>
            <th class="py-3 px-4 border-b border-gold">Price</th>
            <th class="py-3 px-4 border-b border-gold">Quantity</th>
            <th class="py-3 px-4 border-b border-gold">Subtotal</th>
            <th class="py-3 px-4 border-b border-gold">Remove</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($_SESSION['cart'] as $watchId => $qty) {
            $res = mysqli_query($conn, "SELECT * FROM watches WHERE id = $watchId");
            $watch = mysqli_fetch_assoc($res);
            $subtotal = $watch['price'] * $qty;
            $total += $subtotal;
          ?>
          <tr class="border-b border-gold hover:bg-[#222]">
                            <td class="py-3 px-4 border-gold"><img src="../assets/images/watches/<?= $watch['image'] ?>" class="w-16 h-16 object-cover rounded" onerror="this.onerror=null;this.src='../assets/images/watches/default.jpg';this.classList.add('img-fallback');" /></td>
            <td class="py-3 px-4 border-gold"><?= $watch['name'] ?></td>
            <td class="py-3 px-4 border-gold">₹<?= number_format($watch['price']) ?></td>
            <td class="py-3 px-4 border-gold">
              <input type="number" name="qty[<?= $watchId ?>]" value="<?= $qty ?>" min="1" class="w-16 text-black p-1 rounded" />
            </td>
            <td class="py-3 px-4 border-gold">₹<?= number_format($subtotal) ?></td>
            <td class="py-3 px-4 border-gold">
              <a href="remove-from-cart.php?id=<?= $watchId ?>" class="text-red-500 hover:text-red-400">Remove</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4" class="py-3 px-4 text-right font-bold text-gold border-gold">Total:</td>
            <td colspan="2" class="py-3 px-4 font-bold text-gold border-gold">₹<?= number_format($total) ?></td>
          </tr>
        </tfoot>
      </table>
    </div>
    <div class="mt-6 flex justify-end gap-4">
      <button class="bg-gold text-black px-4 py-2 rounded">Update Cart</button>
      <a href="../public/checkout.php" class="bg-green-600 text-white px-4 py-2 rounded">Checkout</a>
    </div>
  </form>
  <?php } ?>
</section>
    <script src="../assets/js/ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script>
if (window.anime) {
  anime({
    targets: 'table, .product-card',
    opacity: [0,1],
    translateY: [40,0],
    delay: anime.stagger(80),
    duration: 700,
    easing: 'easeOutCubic'
  });
}
</script>
<?php require_once __DIR__ . '/../templates/footer.php'; ?> 