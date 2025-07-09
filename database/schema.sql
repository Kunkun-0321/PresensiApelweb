-- Database Schema untuk Sistem Absensi Apel Tingkat
CREATE DATABASE IF NOT EXISTS db_apel;
USE db_apel;

-- Tabel Users (Admin, Petugas, Mahasiswa)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nim VARCHAR(20) UNIQUE,
    nama VARCHAR(100) NOT NULL,
    kelas VARCHAR(20),
    tingkat ENUM('1','2','3','4'),
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','petugas','mahasiswa') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Presensi Apel
CREATE TABLE presensi_apel (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    tanggal DATE NOT NULL,
    waktu TIME NOT NULL,
    status ENUM('Tepat Waktu','Terlambat','Tidak Hadir') NOT NULL,
    keterangan TEXT,
    input_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (input_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_date (user_id, tanggal)
);

-- Tabel Izin/Sakit
CREATE TABLE izin_sakit (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jenis ENUM('Izin','Sakit') NOT NULL,
    alasan TEXT NOT NULL,
    file_bukti VARCHAR(255),
    status_verifikasi ENUM('menunggu','diterima','ditolak') DEFAULT 'menunggu',
    verifikasi_by INT,
    verifikasi_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (verifikasi_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin user
INSERT INTO users (username, password, role, nama) VALUES 
('admin001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator'),
('petugas001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'petugas', 'Petugas Apel');

-- Sample mahasiswa data
INSERT INTO users (nim, nama, kelas, tingkat, username, password, role) VALUES 
('2024001', 'Ahmad Rizki', 'TI-1A', '1', '2024001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa'),
('2024002', 'Siti Nurhaliza', 'TI-1B', '1', '2024002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa'),
('2023001', 'Budi Santoso', 'TI-2A', '2', '2023001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa');
