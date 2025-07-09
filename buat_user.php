<?php
require_once 'config/Database.php';

$username = 'admin001';
$password = 'admin123';  // ganti sesuai kebutuhan
$role     = 'admin';     // bisa: admin, petugas, mahasiswa

// Enkripsi password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert ke DB
$conn = Database::getConnection();
$sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$username, $hashedPassword, $role]);

echo "User berhasil ditambahkan!";
