<?php
// ============================================
// Model Absensi: Updated to use single database config
// ============================================

require_once __DIR__ . '/../config/database.php';

class Absensi
{
    // Cek apakah user sudah presensi di tanggal tertentu
    public static function sudahPresensi($user_id, $tanggal)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) FROM presensi_apel WHERE user_id = ? AND tanggal = ?");
        $stmt->execute([$user_id, $tanggal]);
        return $stmt->fetchColumn() > 0;
    }

    // Simpan data presensi baru
    public static function simpanPresensi($data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("
            INSERT INTO presensi_apel (user_id, tanggal, waktu, status, keterangan, input_by)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['user_id'],
            $data['tanggal'],
            $data['waktu'],
            $data['status'],
            $data['keterangan'],
            $data['input_by']
        ]);
    }

    // Ambil laporan presensi berdasarkan tanggal dan tingkat
    public static function getLaporanPresensi($tanggal, $tingkat = '')
    {
        $conn = Database::getConnection();

        $sql = "
            SELECT u.username AS nim, u.nama, u.kelas, u.tingkat, p.status, p.keterangan, p.waktu
            FROM presensi_apel p
            JOIN users u ON p.user_id = u.id
            WHERE p.tanggal = ?
        ";

        $params = [$tanggal];
        if ($tingkat !== '') {
            $sql .= " AND u.tingkat = ?";
            $params[] = $tingkat;
        }

        $sql .= " ORDER BY p.waktu ASC";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil mahasiswa yang belum presensi pada tanggal dan tingkat tertentu
    public static function getTidakPresensi($tanggal, $tingkat)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("
            SELECT u.username as nim, u.nama, u.kelas, u.tingkat
            FROM users u
            WHERE u.role = 'mahasiswa'
              AND u.tingkat = ?
              AND NOT EXISTS (
                  SELECT 1
                  FROM presensi_apel p
                  WHERE p.user_id = u.id AND p.tanggal = ?
              )
            ORDER BY u.nama ASC
        ");
        $stmt->execute([$tingkat, $tanggal]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get presensi terbaru untuk dashboard
    public static function getPresensiTerbaru($limit = 10, $tanggal = null)
    {
        $conn = Database::getConnection();
        
        if (!$tanggal) $tanggal = date('Y-m-d');
        
        $stmt = $conn->prepare("
            SELECT p.waktu, u.username as nim, u.nama, p.status, p.keterangan 
            FROM presensi_apel p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.tanggal = ? 
            ORDER BY p.waktu DESC 
            LIMIT $limit
        ");
        $stmt->execute([$tanggal]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get statistik presensi
    public static function getStatistik($tanggal = null)
    {
        $conn = Database::getConnection();
        
        if (!$tanggal) $tanggal = date('Y-m-d');
        
        return getStatistikHarian($conn, $tanggal);
    }
}
?>
