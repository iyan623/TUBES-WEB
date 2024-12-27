<?php
require 'connect.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
}

$menus = $pdo->query("SELECT * FROM menu")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD Menu</title>
</head>
<body>
    <h1>Kelola Menu</h1>
    <form method="POST">
        <label>Nama Menu:</label>
        <input type="text" name="nama_menu" required><br>
        <label>Deskripsi:</label>
        <input type="text" name="deskripsi" required><br>
        <label>Harga:</label>
        <input type="number" name="harga" step="0.01" required><br>
        <label>Gambar URL:</label>
        <input type="text" name="gambar" required><br>
        <button type="submit">Tambah Menu</button>
    </form>
    <h2>Menu Tersedia</h2>
    <ul>
        <?php foreach ($menus as $menu): ?>
            <li><?= htmlspecialchars($menu['nama_menu']) ?> - Rp. <?= $menu['harga'] ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
