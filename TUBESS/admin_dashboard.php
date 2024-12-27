<?php 
require 'connect.php'; 
session_start(); 

// Periksa apakah peran pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
    header('Location: user_dashboard.php'); 
    exit; 
}

// Proses Hapus Menu
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) { 
    try { 
        $stmt = $pdo->prepare("DELETE FROM menu WHERE id = ?"); 
        $stmt->execute([$_GET['delete_id']]); 
        header('Location: admin_dashboard.php'); 
        exit(); 
    } catch (PDOException $e) { 
        echo "Kesalahan: " . $e->getMessage(); 
    } 
}

// Proses Edit Menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    try {
        $stmt = $pdo->prepare("UPDATE menu SET nama_menu = ?, deskripsi = ?, harga = ?, gambar = ? WHERE id = ?");
        $gambar = $_FILES['gambar']['name'] ? 'uploads/' . basename($_FILES['gambar']['name']) : $_POST['current_gambar'];
        if ($_FILES['gambar']['name']) {
            move_uploaded_file($_FILES['gambar']['tmp_name'], $gambar);
        }
        $stmt->execute([
            $_POST['nama_menu'],
            $_POST['deskripsi'],
            $_POST['harga'],
            $gambar,
            $_POST['edit_id']
        ]);
        header('Location: admin_dashboard.php');
        exit();
    } catch (PDOException $e) {
        echo "Kesalahan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dasbor</title>
    <link rel="stylesheet" href="style4.css">

    <script>
        // Validasi formulir sebelum dikirimkan untuk Tambah Menu
        function validasiAddMenuForm() {
            const nama_menu = document.forms["addMenuForm"]["nama_menu"].value;
            const deskripsi = document.forms["addMenuForm"]["deskripsi"].value;
            const harga = document.forms["addMenuForm"]["harga"].value;

            if (nama_menu.trim() === "" || deskripsi.trim() === "" || harga.trim() === "") {
                alert("Harap isi semua bidang dengan benar.");
                return false;
            }

            return confirm("Apakah Anda yakin ingin menambahkan menu ini?");
        }

        // Validasi formulir sebelum dikirimkan untuk Edit Menu
        function validasiEditMenuForm() {
            const nama_menu = document.forms["editMenuForm"]["nama_menu"].value;
            const deskripsi = document.forms["editMenuForm"]["deskripsi"].value;
            const harga = document.forms["editMenuForm"]["harga"].value;

            if (nama_menu.trim() === "" || deskripsi.trim() === "" || harga.trim() === "") {
                alert("Harap isi semua bidang dengan benar.");
                return false;
            }

            return confirm("Apakah Anda yakin ingin mengedit menu ini?");
        }

        // Konfirmasi sebelum menghapus menu
        function konfirmasiDelete(id) {
            if (confirm("Apakah Anda yakin ingin menghapus menu ini?")) {
                window.location.href = 'admin_dashboard.php?delete_id=' + id;
            }
        }
    </script>
</head>

<body>
    <h1>Admin Dasbor</h1>

    <!-- Formulir Tambah Menu dengan Konfirmasi -->
    <form name="addMenuForm" method="POST" action="add_menu.php" enctype="multipart/form-data" onsubmit="return validasiAddMenuForm()">
        <h2>Tambah Menu</h2>
        <label for="nama_menu">Nama Menu:</label>
        <input type="text" name="nama_menu" required><br>
        <label for="deskripsi">Deskripsi:</label>
        <input type="text" name="deskripsi" required><br>
        <label for="harga">Harga:</label>
        <input type="number" name="harga" step="0.01" required><br>
        <label for="gambar">Gambar:</label>
        <input type="file" name="gambar" accept="image/*"><br>
        <button type="submit">Tambah Menu</button>
    </form>

    <hr>

    <!-- Tombol untuk kembali ke dashboard awal -->
    <form action="dashboard.php" method="GET">
        <button type="submit">Kembali ke Dashboard Awal</button>
    </form>

    <hr>

    <!-- Formulir Edit Menu -->
    <?php if (isset($_GET['id'])): 
        $stmt = $pdo->prepare("SELECT * FROM menu WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $menu = $stmt->fetch();
    ?>
        <h2>Edit Menu</h2>
        <form name="editMenuForm" method="POST" enctype="multipart/form-data" onsubmit="return validasiEditMenuForm()">
            <input type="hidden" name="edit_id" value="<?php echo $menu['id']; ?>">
            <input type="hidden" name="current_gambar" value="<?php echo $menu['gambar']; ?>">
            <label for="nama_menu">Nama Menu:</label>
            <input type="text" name="nama_menu" value="<?php echo htmlspecialchars($menu['nama_menu']); ?>" required><br>
            <label for="deskripsi">Deskripsi:</label>
            <input type="text" name="deskripsi" value="<?php echo htmlspecialchars($menu['deskripsi']); ?>" required><br>
            <label for="harga">Harga:</label>
            <input type="number" name="harga" value="<?php echo $menu['harga']; ?>" step="0.01" required><br>
            <label for="gambar">Gambar:</label>
            <input type="file" name="gambar" accept="image/*"><br>
            <button type="submit">Simpan Perubahan</button>
        </form>
    <?php endif; ?>

    <!-- Daftar Menu dengan fitur CRUD -->
    <h2>Daftar Menu</h2>
    <?php 
    $stmt = $pdo->query("SELECT * FROM menu");
    $menu_items = $stmt->fetchAll();
    ?>

    <table border="1" cellpadding="8">
        <tr>
            <th>Nama Menu</th>
            <th>Deskripsi</th>
            <th>Harga</th>
            <th>Gambar</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($menu_items as $item) : ?>
            <tr>
                <td><?php echo htmlspecialchars($item['nama_menu']); ?></td>
                <td><?php echo htmlspecialchars($item['deskripsi']); ?></td>
                <td><?php echo "Rp " . number_format($item['harga']); ?></td>
                <td>
                    <?php if ($item['gambar']) : ?>
                        <img src="<?php echo htmlspecialchars($item['gambar']); ?>" width="100" alt="Gambar Menu">
                    <?php else : ?>
                        Tidak ada gambar
                    <?php endif; ?>
                </td>
                <td>
                    <a href="admin_dashboard.php?id=<?php echo $item['id']; ?>">Edit</a> |
                    <a href="javascript:void(0)" onclick="konfirmasiDelete(<?php echo $item['id']; ?>)">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>
