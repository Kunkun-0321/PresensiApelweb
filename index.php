<?php
// index.php - Entry point utama aplikasi

session_start();

// Jika sudah login, arahkan sesuai role
if (isset($_SESSION['user'])) {
  $role = $_SESSION['user']['role'];

  switch ($role) {
    case 'admin':
      header("Location: views/admin/dashboard.php");
      exit;
    case 'petugas':
      header("Location: views/petugas/dashboard.php");
      exit;
    case 'mahasiswa':
      header("Location: views/mahasiswa/dashboard.php");
      exit;
    default:
      session_destroy();
      header("Location: views/auth/login.php");
      exit;
  }
} else {
  header("Location: views/auth/login.php");
  exit;
}
?>
