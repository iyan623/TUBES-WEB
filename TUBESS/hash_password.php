<?php
// Sertakan koneksi database
require 'connect.php';

try {
    // Hash password untuk admin
    $passwordAdmin = password_hash('admin', PASSWORD_DEFAULT);

    // Cek apakah pengguna admin sudah ada
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $checkStmt->execute(['admin']);
    $adminExists = $checkStmt->fetchColumn();

    if (!$adminExists) {
        // Tambahkan admin jika belum ada
        $insertStmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $insertStmt->execute(['admin', $passwordAdmin, 'admin']);
        echo "Admin berhasil ditambahkan.<br>";
    } else {
        // Perbarui password dan role jika admin sudah ada
        $updateStmt = $pdo->prepare("UPDATE users SET password = ?, role = ? WHERE username = ?");
        $updateStmt->execute([$passwordAdmin, 'admin', 'admin']);
        echo "Admin sudah ada. Password dan role telah diperbarui.<br>";
    }

    echo "Operasi selesai! Anda sekarang dapat login dengan username 'admin' dan password 'admin'.";
} catch (PDOException $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
}
?>
