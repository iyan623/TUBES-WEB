<?php
require 'connect.php';
session_start();

// Periksa apakah role pengguna adalah admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: user_dashboard.php');
    exit;
}

// Proses form jika ada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_menu = $_POST['nama_menu'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $gambar = '';

    // Proses file gambar jika diunggah
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/'; // Pastikan direktori ini ada dan memiliki izin tulis
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $gambar = $upload_dir . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $gambar);
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO menu (nama_menu, deskripsi, harga, gambar) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama_menu, $deskripsi, $harga, $gambar]);
        header('Location: admin_dashboard.php');
        exit();
    } catch (PDOException $e) {
        echo "Kesalahan: " . $e->getMessage();
    }
}
?>
