<?php
// ============================================
// Model IzinSakit: untuk ajukan dan verifikasi izin
// ============================================

require_once __DIR__ . '/../config/database.php';

class IzinSakit {
    public static function ajukan($data) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO izin_sakit (user_id, tanggal, jenis, alasan, file_bukti) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['user_id'], $data['tanggal'], $data['jenis'],
            $data['alasan'], $data['file_bukti']
        ]);
    }

    public static function sudahAjukanHariIni($user_id, $tanggal, $jenis) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM izin_sakit WHERE user_id = ? AND tanggal = ? AND jenis = ?");
        $stmt->execute([$user_id, $tanggal, $jenis]);
        return $stmt->fetchColumn() > 0;
    }

    public static function getPengajuanBelumVerif() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM izin_sakit WHERE status_verifikasi = 'menunggu'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function verifikasi($id, $status, $verifikator) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE izin_sakit SET status_verifikasi = ?, verifikasi_by = ? WHERE id = ?");
        return $stmt->execute([$status, $verifikator, $id]);
    }
}
?>
