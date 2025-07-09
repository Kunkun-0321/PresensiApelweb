<?php
// =============================================
// AuthController: Login dan Logout pengguna
// =============================================

session_start();
require_once __DIR__ . '/../models/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $user = User::getByUsername($_POST['username']);
    
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user'] = $user;
        // Arahkan ke dashboard sesuai role
        if ($user['role'] === 'admin') {
            header('Location: /ProjectFinal/views/admin/dashboard.php');
        } elseif ($user['role'] === 'petugas') {
            header('Location: /ProjectFinal/views/petugas/dashboard.php');
        } elseif ($user['role'] === 'mahasiswa') {
            header('Location: /ProjectFinal/views/mahasiswa/dashboard.php');
        }
        exit;
    } else {
        header('Location: /ProjectFinal/views/auth/login.php?error=1');
        exit;
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../views/auth/login.php');
    exit;
}
?>
