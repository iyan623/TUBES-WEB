<?php
session_start();
require 'connect.php';

// Periksa akses pengguna
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

// Proses penghapusan pesanan
if (isset($_GET['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([$_GET['delete_id'], $_SESSION['user_id']]);
        header('Location: order_online.php');
        exit;
    } catch (PDOException $e) {
        die("Kesalahan saat menghapus pesanan: " . $e->getMessage());
    }
}

// Proses pemesanan saat formulir di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $menu_id = $_POST['menu_id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id'];

    try {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, menu_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $menu_id, $quantity]);
            header('Location: order_online.php');
            exit;
        } elseif ($_POST['action'] === 'edit') {
            $order_id = $_POST['order_id'];
            $stmt = $pdo->prepare("UPDATE orders SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$quantity, $order_id, $user_id]);
            header('Location: order_online.php');
            exit;
        }
    } catch (PDOException $e) {
        die("Kesalahan saat memproses pesanan: " . $e->getMessage());
    }
}

// Ambil daftar menu dari database
$stmt_menu = $pdo->query("SELECT * FROM menu");
$menu_items = $stmt_menu->fetchAll(PDO::FETCH_ASSOC);

// Ambil daftar pesanan pengguna untuk ditampilkan
$stmt_orders = $pdo->prepare("SELECT o.*, m.nama_menu FROM orders o JOIN menu m ON o.menu_id = m.id WHERE o.user_id = ?");
$stmt_orders->execute([$_SESSION['user_id']]);
$orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

// Ambil data untuk formulir edit jika ada
$edit_order = null;
if (isset($_GET['id'])) {
    $stmt_edit = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt_edit->execute([$_GET['id'], $_SESSION['user_id']]);
    $edit_order = $stmt_edit->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pemesanan Online</title>
  <link rel="stylesheet" href="style7.css">
  <script>
    function confirmDelete(id) {
      if (confirm("Apakah Anda yakin ingin menghapus pesanan ini?")) {
        window.location.href = 'order_online.php?delete_id=' + id;
      }
    }

    function confirmAdd() {
      return confirm("Apakah Anda yakin ingin menambahkan pesanan ini?");
    }

    function confirmEdit() {
      return confirm("Apakah Anda yakin ingin menyimpan perubahan pada pesanan ini?");
    }
  </script>
</head>
<body>
  <!-- Judul -->
  <h1>Pemesanan Online</h1>
  
  <!-- Tombol Kembali ke Dashboard Awal -->
  <div style="text-align: center;">
    <button onclick="window.location.href='dashboard.php'">Kembali ke Dashboard Awal</button>
  </div>
  <hr>
  
  <!-- Formulir Pemesanan Baru atau Edit -->
  <?php if (!$edit_order) : ?>
  <form name="orderForm" method="POST" onsubmit="return confirmAdd()">
    <label for="menu">Pilih Menu:</label>
    <select id="menu" name="menu_id">
      <option value="">-- Pilih Menu --</option>
      <?php foreach ($menu_items as $item) : ?>
        <option value="<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['nama_menu']); ?> - Rp <?php echo number_format($item['harga']); ?></option>
      <?php endforeach; ?>
    </select><br><br>
    
    <label for="quantity">Jumlah:</label>
    <input type="number" id="quantity" name="quantity" min="1" value="1"><br><br>
    
    <button type="submit" name="action" value="add">Pesan Sekarang</button>
  </form>
  <?php else : ?>
  <form name="editForm" method="POST" onsubmit="return confirmEdit()">
    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($edit_order['id']); ?>">
    <label for="quantity">Jumlah:</label>
    <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($edit_order['quantity']); ?>" min="1"><br><br>
    <button type="submit" name="action" value="edit">Simpan Perubahan</button>
  </form>
  <?php endif; ?>

  <hr>
  <h2 style="text-align: center;">Daftar Pesanan Anda</h2>
  <table>
    <tr>
      <th>Nama Menu</th>
      <th>Jumlah</th>
      <th>Aksi</th>
    </tr>
    <?php foreach ($orders as $order) : ?>
      <tr>
        <td><?php echo htmlspecialchars($order['nama_menu']); ?></td>
        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
        <td>
          <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $order['id']; ?>)">Hapus</a> |
          <a href="order_online.php?id=<?php echo $order['id']; ?>">Edit</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
