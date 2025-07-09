<?php
// =============================================
// MahasiswaController: Tambah/update/hapus mahasiswa
// =============================================

session_start();
require_once __DIR__ . '/../models/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nim'      => $_POST['nim'],
        'nama'     => $_POST['nama'],
        'kelas'    => $_POST['kelas'],
        'tingkat'  => $_POST['tingkat'],
        'username' => $_POST['username'],
        'password' => $_POST['password'] // hanya digunakan saat insert
    ];

    if (isset($_POST['id'])) {
        User::updateMahasiswa($_POST['id'], $data);
    } else {
        User::insertMahasiswa($data);
    }

    header('Location: ../views/admin/data_mahasiswa.php');
    exit;
}

// Hapus mahasiswa
if (isset($_GET['hapus'])) {
    User::deleteById($_GET['hapus']);
    header('Location: ../views/admin/data_mahasiswa.php');
    exit;
}
?>
