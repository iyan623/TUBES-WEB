<?php
session_start();

// Periksa sesi jika pengguna belum login
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Awal</title>
  <link rel="stylesheet" href="style5.css">
</head>
<body>
  <!-- Dashboard Kontainer Utama -->
  <div class="dashboard-container">
    <h1>Selamat Datang di Dashboard Awal</h1>
    
    <?php if ($_SESSION['role'] === 'admin') : ?>
      <!-- Menu untuk Admin -->
      <div class="user-menu">
        <a href="admin_dashboard.php">Kelola Menu</a>
        <a href="admin_orders.php">Admin Orders</a>
      </div>
    <?php elseif ($_SESSION['role'] === 'user') : ?>
      <!-- Menu untuk User -->
      <div class="user-menu">
        <a href="user_dashboard.php">Lihat Menu</a>
        <a href="order_online.php">Online Orders</a>
      </div>

      <!-- Profil Anda di bawah menu dengan jarak rapi -->
      <div class="user-menu">
        <a href="user_profile.php">Profil Anda</a>
      </div>
    <?php endif; ?>

    <br><br>
    <a href="logout.php">Logout</a>
  </div>
</body>
</html>
