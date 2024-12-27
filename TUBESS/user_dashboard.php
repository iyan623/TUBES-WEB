<?php
require 'connect.php';
session_start();

// Periksa sesi pengguna
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

// Ambil daftar menu dari database
try {
    $stmt = $pdo->query("SELECT * FROM menu");
    $menu_items = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Terjadi kesalahan saat mengambil data menu: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link rel="stylesheet" href="style6.css">
</head>
<body>
    <!-- Header -->
    <h1>Selamat Datang di Dashboard Anda</h1>
    
    <!-- Daftar Menu Section -->
    <h2>Daftar Menu</h2>
    <table>
        <tr>
            <th>Nama Menu</th>
            <th>Deskripsi</th>
            <th>Harga</th>
            <th>Gambar</th>
        </tr>
        <?php foreach ($menu_items as $item) : ?>
            <tr>
                <td><?php echo htmlspecialchars($item['nama_menu']); ?></td>
                <td><?php echo htmlspecialchars($item['deskripsi']); ?></td>
                <td><?php echo "Rp " . number_format($item['harga']); ?></td>
                <td>
                    <?php if ($item['gambar']): ?>
                        <img src="<?php echo htmlspecialchars($item['gambar']); ?>" width="120" alt="Menu">
                    <?php else: ?>
                        Tidak ada gambar
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Tombol Kembali ke Dashboard Awal -->
    <form action="dashboard.php" method="GET">
        <button type="submit" class="btn-back">Kembali ke Dashboard Awal</button>
    </form>
</body>
</html>
