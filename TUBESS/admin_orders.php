<?php
require 'connect.php';
session_start();

// Periksa jika pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: user_dashboard.php');
    exit;
}

// Ambil semua data pemesanan dengan informasi nama menu
$stmt = $pdo->query("
    SELECT o.id, o.user_id, m.nama_menu, o.quantity, (o.quantity * m.harga) AS total_price
    FROM orders o
    JOIN menu m ON o.menu_id = m.id
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Pemesanan</title>
  <link rel="stylesheet" href="style8.css">
</head>
<body>
  <h1>Laporan Pemesanan</h1>

  <table>
    <tr>
      <th>ID Pesanan</th>
      <th>User ID</th>
      <th>Menu Item</th>
      <th>Jumlah</th>
      <th>Total Harga</th>
    </tr>
    <?php foreach ($orders as $order): ?>
      <tr>
        <td><?php echo htmlspecialchars($order['id']); ?></td>
        <td><?php echo htmlspecialchars($order['user_id']); ?></td>
        <td><?php echo htmlspecialchars($order['nama_menu']); ?></td>
        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
        <td><?php echo "Rp " . number_format($order['total_price']); ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
