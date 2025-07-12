<?php
// =============================================
// AbsensiController: Enhanced barcode attendance processing
// =============================================

session_start();
require_once __DIR__ . '/../models/Absensi.php';
require_once __DIR__ . '/../models/User.php';

// Set JSON header for AJAX responses
header('Content-Type: application/json');

// Pastikan hanya petugas yang boleh akses
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'scan_presensi':
            handleScanPresensi();
            break;
            
        case 'check_duplicate':
            handleCheckDuplicate();
            break;
            
        default:
            handleLegacyPresensi();
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'get_stats':
            handleGetStats();
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
}

function handleScanPresensi() {
    try {
        $barcodeData = $_POST['data_barcode'] ?? '';
        $status = $_POST['status'] ?? '';
        $keterangan = $_POST['keterangan'] ?? '';
        $tingkatInput = $_POST['tingkat_input'] ?? '';
        
        if (empty($barcodeData) || empty($status)) {
            throw new Exception('Data barcode dan status harus diisi');
        }
        
        // Parse barcode: NIM|Nama|Kelas|Tingkat|Timestamp
        $parts = explode('|', $barcodeData);
        if (count($parts) < 4) {
            throw new Exception('Format barcode tidak valid');
        }
        
        $nim = $parts[0];
        $nama = $parts[1];
        $kelas = $parts[2];
        $tingkat = $parts[3];
        
        // Validate tingkat
        if ($tingkat !== $tingkatInput) {
            throw new Exception('Tingkat pada barcode tidak sesuai dengan tingkat yang dipilih');
        }
        
        // Get user by NIM
        $user = User::getByUsername($nim);
        if (!$user) {
            throw new Exception('Mahasiswa dengan NIM ' . $nim . ' tidak ditemukan');
        }
        
        // Validate user data matches barcode
        if ($user['nama'] !== $nama || $user['tingkat'] !== $tingkat) {
            throw new Exception('Data pada barcode tidak sesuai dengan data di sistem');
        }
        
        $tanggal = date('Y-m-d');
        
        // Check if already present today
        if (Absensi::sudahPresensi($user['id'], $tanggal)) {
            throw new Exception('Mahasiswa sudah melakukan presensi hari ini');
        }
        
        // Determine if late based on current time (example: late if after 07:30)
        $currentTime = date('H:i:s');
        $lateThreshold = '07:30:00';
        
        if ($status === 'Tepat Waktu' && $currentTime > $lateThreshold) {
            // Auto-correct status if current time indicates late
            $status = 'Terlambat';
            $keterangan = ($keterangan ? $keterangan . ' | ' : '') . 'Auto-corrected: Waktu scan ' . date('H:i');
        }
        
        $data = [
            'user_id'    => $user['id'],
            'tanggal'    => $tanggal,
            'waktu'      => $currentTime,
            'status'     => $status,
            'keterangan' => $keterangan,
            'input_by'   => $_SESSION['user']['id']
        ];
        
        if (Absensi::simpanPresensi($data)) {
            echo json_encode([
                'success' => true,
                'message' => 'Presensi berhasil disimpan',
                'data' => [
                    'nim' => $nim,
                    'nama' => $nama,
                    'status' => $status,
                    'waktu' => $currentTime
                ]
            ]);
        } else {
            throw new Exception('Gagal menyimpan data presensi');
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

function handleCheckDuplicate() {
    try {
        $nim = $_POST['nim'] ?? '';
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        
        if (empty($nim)) {
            throw new Exception('NIM harus diisi');
        }
        
        $user = User::getByUsername($nim);
        if (!$user) {
            echo json_encode(['exists' => false]);
            return;
        }
        
        $exists = Absensi::sudahPresensi($user['id'], $tanggal);
        echo json_encode(['exists' => $exists]);
        
    } catch (Exception $e) {
        echo json_encode(['exists' => false, 'error' => $e->getMessage()]);
    }
}

function handleGetStats() {
    try {
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        
        $conn = Database::getConnection();
        
        // Count by status
        $stmt = $conn->prepare("
            SELECT 
                SUM(CASE WHEN status = 'Tepat Waktu' THEN 1 ELSE 0 END) as tepat_waktu,
                SUM(CASE WHEN status = 'Terlambat' THEN 1 ELSE 0 END) as terlambat
            FROM presensi_apel 
            WHERE tanggal = ?
        ");
        $stmt->execute([$tanggal]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Count total students who should attend (example: all active students)
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'mahasiswa'");
        $stmt->execute();
        $total = $stmt->fetchColumn();
        
        $hadir = ($stats['tepat_waktu'] ?? 0) + ($stats['terlambat'] ?? 0);
        $tidak_hadir = $total - $hadir;
        
        echo json_encode([
            'tepat_waktu' => $stats['tepat_waktu'] ?? 0,
            'terlambat' => $stats['terlambat'] ?? 0,
            'tidak_hadir' => max(0, $tidak_hadir),
            'total' => $total
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'tepat_waktu' => 0,
            'terlambat' => 0,
            'tidak_hadir' => 0,
            'total' => 0,
            'error' => $e->getMessage()
        ]);
    }
}

function handleLegacyPresensi() {
    // Legacy support for old form submissions
    try {
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $keterangan = isset($_POST['keterangan']) ? htmlspecialchars($_POST['keterangan']) : '';

        if ($user_id <= 0) {
            throw new Exception('User ID tidak valid');
        }

        $data = [
            'user_id'    => $user_id,
            'tanggal'    => date('Y-m-d'),
            'waktu'      => date('H:i:s'),
            'status'     => 'hadir',
            'keterangan' => $keterangan,
            'input_by'   => $_SESSION['user']['id']
        ];

        if (!Absensi::sudahPresensi($user_id, $data['tanggal'])) {
            if (Absensi::simpanPresensi($data)) {
                header('Location: ../views/petugas/dashboard.php?success=1');
            } else {
                header('Location: ../views/petugas/dashboard.php?error=save_failed');
            }
        } else {
            header('Location: ../views/petugas/dashboard.php?error=duplicate');
        }
        exit;
        
    } catch (Exception $e) {
        header('Location: ../views/petugas/dashboard.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
?>
