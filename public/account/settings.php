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
  <title>Account Settings | JackoTimespiece</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
  <style>
    body { background: #0c0c0c; }
    .glass {
      background: rgba(24,24,24,0.92);
      backdrop-filter: blur(18px);
      border-radius: 1.7rem;
      box-shadow: 0 8px 40px 0 #c9b37e22, 0 1.5px 0 #c9b37e44;
      border: 2.5px solid #c9b37e;
      position: relative;
      overflow: hidden;
      margin-bottom: 2.5rem;
      transition: box-shadow 0.3s;
    }
    .glass:before {
      content: '';
      position: absolute;
      top: -60%; left: -60%;
      width: 220%; height: 220%;
      background: conic-gradient(from 180deg at 50% 50%, #c9b37e33 0deg, #c9b37e 90deg, #c9b37e33 180deg, #c9b37e 270deg, #c9b37e33 360deg);
      filter: blur(32px);
      z-index: 0;
      animation: borderSpin 8s linear infinite;
    }
    @keyframes borderSpin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .glass > * { position: relative; z-index: 1; }
    .section-title {
      font-family: 'Playfair Display', serif;
      font-size: 2.2rem;
      color: #c9b37e;
      letter-spacing: 0.04em;
      text-shadow: 0 2px 0 #fff2, 0 0 16px #c9b37e44;
      margin-bottom: 2.5rem;
      border-bottom: 2px solid #c9b37e;
      padding-bottom: 0.5rem;
      text-align: center;
    }
    .settings-card {
      border: 1.5px solid #c9b37e;
      border-radius: 1.2rem;
      background: rgba(18,18,18,0.98);
      box-shadow: 0 4px 24px #c9b37e22;
      margin-bottom: 2.2rem;
      padding: 2.2rem 2rem 1.5rem 2rem;
    }
    .settings-card h3 {
      font-family: 'Playfair Display', serif;
      color: #c9b37e;
      font-size: 1.3rem;
      margin-bottom: 1.2rem;
      letter-spacing: 0.03em;
    }
    .input {
      width: 100%;
      margin-bottom: 1.2rem;
      padding: 0.85rem 1.1rem;
      color: #000;
      border-radius: 0.75rem;
      border: 1.5px solid #c9b37e;
      background: #181818;
      font-size: 1.08rem;
      transition: border 0.2s, box-shadow 0.2s;
      outline: none;
      font-family: 'Inter', Arial, sans-serif;
    }
    .input:focus {
      border-color: #c9b37e;
      box-shadow: 0 0 0 2px #c9b37e55;
      background: #232323;
      color: #fff;
    }
    .btn-gold {
      background: #c9b37e;
      color: #000;
      font-weight: 600;
      border-radius: 999px;
      padding: 0.9rem 0;
      font-size: 1.1rem;
      box-shadow: 0 0 16px #c9b37e55;
      transition: box-shadow 0.2s, transform 0.2s, background 0.2s;
      width: 100%;
      font-family: 'Playfair Display', serif;
      letter-spacing: 0.03em;
    }
    .btn-gold:hover {
      background: #fff;
      color: #c9b37e;
      box-shadow: 0 0 32px #c9b37e99;
      transform: scale(1.03);
    }
    .profile-pic-frame {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1.2rem;
    }
    .profile-pic {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
      border: 3.5px solid #c9b37e;
      box-shadow: 0 0 0 8px #181818, 0 2px 16px #c9b37e33;
      background: #232323;
      filter: grayscale(0.15) contrast(1.1);
    }
    .star {
      color: #c9b37e;
      font-size: 1.7rem;
      cursor: pointer;
      transition: color 0.2s;
      text-shadow: 0 2px 0 #fff2, 0 0 8px #c9b37e44;
    }
    .star.inactive { color: #444; }
    .glass .bg-red-700 { background: #a94442; }
    textarea.input { min-height: 80px; }
  </style>
</head>
<body class="bg-black text-white font-sans min-h-screen">
  <?php require_once '../../templates/header.php'; ?>
  <section class="container mx-auto px-6 py-16 text-white max-w-2xl">
    <div class="section-title">Account Settings</div>

    <!-- Profile Picture Upload -->
    <div class="settings-card glass">
      <h3>Profile Picture</h3>
      <div class="profile-pic-frame">
        <img src="../../assets/images/users/<?= htmlspecialchars($user['image'] ?? 'default.png') ?>" class="profile-pic" alt="Profile">
      </div>
      <form method="POST" action="update-profile-pic.php" enctype="multipart/form-data">
        <input type="file" name="profile_pic" accept="image/*" class="mb-3 text-white">
        <button type="submit" class="btn-gold">Upload</button>
      </form>
    </div>

    <!-- Name & Email -->
    <div class="settings-card glass">
      <h3>Update Name & Email</h3>
      <form method="POST" action="update-settings.php">
        <label class="block mb-2">Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="input">
        <label class="block mt-4 mb-2">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="input">
        <button type="submit" class="mt-6 btn-gold">Update</button>
      </form>
    </div>

    <!-- Password -->
    <div class="settings-card glass">
      <h3>Change Password</h3>
      <form method="POST" action="update-password.php">
        <label class="block mb-2">Current Password</label>
        <input type="password" name="old_password" class="input">
        <label class="block mt-4 mb-2">New Password</label>
        <input type="password" name="new_password" class="input">
        <label class="block mt-4 mb-2">Confirm New Password</label>
        <input type="password" name="confirm_password" class="input">
        <button type="submit" class="mt-6 btn-gold">Change Password</button>
      </form>
    </div>

    <!-- Message to Admin -->
    <div class="settings-card glass">
      <h3>Send a Message to Admin</h3>
      <form method="POST" action="send-message.php">
        <textarea name="message" rows="3" class="input" placeholder="Your message..."></textarea>
        <button type="submit" class="mt-3 btn-gold">Send Message</button>
      </form>
    </div>

    <!-- Rate Website -->
    <div class="settings-card glass">
      <h3>Rate Our Website</h3>
      <form method="POST" action="rate-site.php" id="rate-form">
        <div class="flex items-center mb-3">
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <span class="star inactive" data-value="<?= $i ?>"><i class="fa fa-star"></i></span>
          <?php endfor; ?>
        </div>
        <input type="hidden" name="rating" id="rating-value" value="0">
        <textarea name="comment" rows="2" class="input" placeholder="Optional comment..."></textarea>
        <button type="submit" class="mt-3 btn-gold">Submit Rating</button>
      </form>
    </div>
  </section>
  <?php require_once '../../templates/footer.php'; ?>
  <script>
    // Star rating logic
    document.querySelectorAll('.star').forEach(star => {
      star.addEventListener('mouseover', function() {
        let val = this.getAttribute('data-value');
        document.querySelectorAll('.star').forEach(s => {
          s.classList.toggle('inactive', s.getAttribute('data-value') > val);
        });
      });
      star.addEventListener('mouseout', function() {
        let current = document.getElementById('rating-value').value;
        document.querySelectorAll('.star').forEach(s => {
          s.classList.toggle('inactive', s.getAttribute('data-value') > current);
        });
      });
      star.addEventListener('click', function() {
        let val = this.getAttribute('data-value');
        document.getElementById('rating-value').value = val;
        document.querySelectorAll('.star').forEach(s => {
          s.classList.toggle('inactive', s.getAttribute('data-value') > val);
        });
      });
    });
    // Animate cards
    if (window.anime) {
      anime({
        targets: '.settings-card',
        opacity: [0,1],
        translateY: [40,0],
        delay: anime.stagger(120),
        duration: 900,
        easing: 'easeOutCubic'
      });
    }
  </script>
</body>
</html>