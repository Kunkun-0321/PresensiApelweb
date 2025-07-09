<?php
// =============================================
// BarcodeController: Generate barcode mahasiswa
// =============================================

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'mahasiswa') {
    header('Location: ../views/auth/login.php');
    exit;
}

$data = $_SESSION['user']['nim'] . '|' . $_SESSION['user']['nama'] . '|' . $_SESSION['user']['kelas'] . '|' . $_SESSION['user']['tingkat'];

// Gunakan library QR/barcode (diload di view)
header('Content-Type: text/plain');
echo $data;
?>
