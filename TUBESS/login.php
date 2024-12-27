<?php
require 'connect.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'], $_POST['password'], $_POST['action']) && $_POST['action'] === 'login') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $remember = isset($_POST['remember']);

        try {
            // Periksa apakah pengguna ada di database
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Validasi username dan password
            if ($user && password_verify($password, $user['password'])) {
                // Set session dan cookie jika diperlukan
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                if ($remember) {
                    setcookie('user_id', $user['id'], time() + (86400 * 30), "/"); // 30 hari
                    setcookie('user_role', $user['role'], time() + (86400 * 30), "/"); // 30 hari
                }

                // Redirect berdasarkan peran pengguna
                if ($user['role'] === 'admin') {
                    header('Location: admin_dashboard.php');
                } else {
                    header('Location: user_dashboard.php');
                }
                exit();
            } else {
                $error = "Username atau password salah.";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <form method="POST">
        <h1>Login</h1>
        <!-- Username -->
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" placeholder="Masukkan Username" required>
        
        <!-- Password -->
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Masukkan Password" required>
        
        <!-- Checkbox -->
        <div class="checkbox-container">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Ingat Saya</label>
        </div>
        
        <!-- Login Button -->
        <button type="submit" name="action" value="login">Login</button>

        <!-- Register Link -->
        <a href="register.php" class="register-link">Daftar Sekarang</a>
    </form>

    <!-- Error Message -->
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
</body>
</html>
