<?php
session_start();
require 'connect.php';

// Periksa sesi
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Ambil data pengguna dari database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Perbarui Username dan Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $new_username = $_POST['username'];
    $new_password = $_POST['password'];
    
    if (empty($new_username) || empty($new_password)) {
        echo "<script>alert('Username dan Password harus diisi!');</script>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $update_stmt = $pdo->prepare(
            "UPDATE users SET username = ?, password = ? WHERE id = ?"
        );
        $update_stmt->execute([$new_username, $hashed_password, $_SESSION['user_id']]);
        
        echo "<script>alert('Informasi berhasil diperbarui!'); window.location.href = 'user_profile.php';</script>";
        exit;
    }
}

// Hapus akun pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $delete_stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $delete_stmt->execute([$_SESSION['user_id']]);
    
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="style9.css">
</head>
<body>
    <h1>Profil Anda</h1>
    
    <!-- Form untuk Update Username dan Password -->
    <form method="POST">
        <label>Username Baru:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        
        <label>Password Baru:</label>
        <input type="password" name="password" placeholder="Masukkan Password Baru" required>
        
        <button type="submit" name="update">Perbarui Informasi</button>
    </form>
    
    <br>
    
    <!-- Tombol Hapus Akun -->
    <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun Anda?');">
        <button type="submit" name="delete">Hapus Akun</button>
    </form>
    
    <br>
    <a href="user_dashboard.php">Kembali ke Dashboard</a>
</body>
</html>
