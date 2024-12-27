<?php
require 'connect.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    try {
        // Cek apakah username atau email sudah digunakan
        $stmtCheck = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmtCheck->execute([$username, $email]);
        if ($stmtCheck->rowCount() > 0) {
            $error = "Username atau email sudah digunakan.";
        } else {
            // Hash password sebelum disimpan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Simpan data pengguna
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, phone, address, role) VALUES (?, ?, ?, ?, ?, 'user')");
            $stmt->execute([$username, $hashed_password, $email, $phone, $address]);

            header('Location: login.php');
            exit();
        }
    } catch (PDOException $e) {
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun</title>
    <link rel="stylesheet" href="style3.css">
</head>
<body>
    <form method="POST">
        <h1>Daftar Akun Baru</h1>

        <!-- Username -->
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" placeholder="Masukkan Username" required>

        <!-- Password -->
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Masukkan Password" required>

        <!-- Email -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Masukkan Email" required>

        <!-- Phone -->
        <label for="phone">Nomor Telepon:</label>
        <input type="text" id="phone" name="phone" placeholder="Masukkan Nomor Telepon" required>

        <!-- Address -->
        <label for="address">Alamat:</label>
        <textarea id="address" name="address" placeholder="Masukkan Alamat" required></textarea>

        <!-- Register Button -->
        <button type="submit">Daftar</button>

        <!-- Link to Login -->
        <a href="login.php">Kembali ke Halaman Login</a>
        
    </form>

    <!-- Error Message -->
    <?php if ($error): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>
</body>
</html>
