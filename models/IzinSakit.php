<?php
// ============================================
// Model IzinSakit: Updated to use single database config
// ============================================

require_once __DIR__ . '/../config/database.php';

class IzinSakit {
    public static function ajukan($data) {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("
            INSERT INTO izin_sakit (user_id, tanggal, jenis, alasan, file_bukti) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['user_id'], 
            $data['tanggal'], 
            $data['jenis'],
            $data['alasan'], 
            $data['file_bukti']
        ]);
    }

    public static function sudahAjukanHariIni($user_id, $tanggal, $jenis) {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("
            SELECT COUNT(*) FROM izin_sakit 
            WHERE user_id = ? AND tanggal = ? AND jenis = ?
        ");
        $stmt->execute([$user_id, $tanggal, $jenis]);
        return $stmt->fetchColumn() > 0;
    }

    public static function getPengajuanBelumVerif() {
        $conn = Database::getConnection();
        $stmt = $conn->query("
            SELECT i.*, u.username as nim, u.nama 
            FROM izin_sakit i 
            JOIN users u ON i.user_id = u.id 
            WHERE i.status_verifikasi = 'menunggu'
            ORDER BY i.created_at ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function verifikasi($id, $status, $verifikator) {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("
            UPDATE izin_sakit 
            SET status_verifikasi = ?, verifikasi_by = ?, verifikasi_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$status, $verifikator, $id]);
    }

    public static function getById($id) {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT * FROM izin_sakit WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getByUserId($user_id, $limit = 10) {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("
            SELECT * FROM izin_sakit 
            WHERE user_id = ? 
            ORDER BY tanggal DESC 
            LIMIT ?
        ");
        $stmt->execute([$user_id, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getStatistikVerifikasi() {
        $conn = Database::getConnection();
        $stmt = $conn->query("
            SELECT 
                status_verifikasi,
                COUNT(*) as jumlah
            FROM izin_sakit 
            GROUP BY status_verifikasi
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
