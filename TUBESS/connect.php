<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=coffee', 'root', ''); // Ganti dengan kredensial Anda
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
