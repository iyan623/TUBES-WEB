<?php
require 'connect.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header('Location: user_dashboard.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM menu WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: admin_dashboard.php');
    exit;
}
?>
