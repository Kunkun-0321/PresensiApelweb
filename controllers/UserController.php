<?php
// =======================================
// UserController: proses tambah user
// =======================================

require_once __DIR__ . '/../models/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'tambah') {
    $data = [
        'nim'      => $_POST['nim'] ?? null,
        'nama'     => $_POST['nama'] ?? null,
        'kelas'    => $_POST['kelas'] ?? null,
        'tingkat'  => $_POST['tingkat'] ?? null,
        'username' => $_POST['username'],
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'role'     => $_POST['role']
    ];

    if (User::insertUser($data)) {
        header('Location: ../views/admin/tambah_user.php?success=1');
    } else {
        header('Location: ../views/admin/tambah_user.php?error=1');
    }
    exit;
}
