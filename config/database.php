<?php
// ===============================================
// Unified Database Configuration - Single Source of Truth
// ===============================================

class Database {
    private static $host = 'localhost';
    private static $dbname = 'db_apel';
    private static $username = 'root';
    private static $password = '';
    private static $conn;

    public static function getConnection() {
        if (!self::$conn) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8",
                    self::$username,
                    self::$password
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Koneksi database gagal: " . $e->getMessage());
            }
        }
        return self::$conn;
    }

    // Method untuk backward compatibility
    public static function getPDO() {
        return self::getConnection();
    }
}

// Global variables untuk backward compatibility dengan kode lama
$conn = Database::getConnection();
$pdo = Database::getConnection();

// ===============================================
// Helper Functions - Semua fungsi database di satu tempat
// ===============================================

function getRiwayatPresensiMahasiswa($conn, $nim) {
    $stmt = $conn->prepare("
        SELECT p.tanggal, p.waktu, p.status, p.keterangan 
        FROM presensi_apel p 
        JOIN users u ON p.user_id = u.id 
        WHERE u.username = ? 
        ORDER BY p.tanggal DESC, p.waktu DESC 
        LIMIT 20
    ");
    $stmt->execute([$nim]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStatusIzinSakitMahasiswa($conn, $nim) {
    $stmt = $conn->prepare("
        SELECT i.tanggal, i.jenis, i.status_verifikasi as status, i.alasan 
        FROM izin_sakit i 
        JOIN users u ON i.user_id = u.id 
        WHERE u.username = ? 
        ORDER BY i.tanggal DESC 
        LIMIT 10
    ");
    $stmt->execute([$nim]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPengajuanIzinMenunggu($conn) {
    $stmt = $conn->query("
        SELECT i.*, u.username as nim, u.nama, i.file_bukti as bukti
        FROM izin_sakit i 
        JOIN users u ON i.user_id = u.id 
        WHERE i.status_verifikasi = 'menunggu' 
        ORDER BY i.created_at ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStatistikHarian($conn, $tanggal = null) {
    if (!$tanggal) $tanggal = date('Y-m-d');
    
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_hadir,
            SUM(CASE WHEN status = 'Tepat Waktu' THEN 1 ELSE 0 END) as tepat_waktu,
            SUM(CASE WHEN status = 'Terlambat' THEN 1 ELSE 0 END) as terlambat
        FROM presensi_apel 
        WHERE tanggal = ?
    ");
    $stmt->execute([$tanggal]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getTotalMahasiswa($conn) {
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'mahasiswa'");
    return $stmt->fetchColumn();
}

// ===============================================
// Database Configuration Settings
// ===============================================

// Timezone setting
date_default_timezone_set('Asia/Jakarta');

// Error reporting untuk development (hapus di production)
if ($_SERVER['SERVER_NAME'] === 'localhost') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
?>
