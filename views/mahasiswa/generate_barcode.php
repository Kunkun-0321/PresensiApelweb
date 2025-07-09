<?php
require_once '../../config/Database.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

$nim = $_SESSION['user']['username'];
$nama = $_SESSION['user']['nama'] ?? 'Unknown';
$kelas = $_SESSION['user']['kelas'] ?? 'Unknown';
$tingkat = $_SESSION['user']['tingkat'] ?? 'Unknown';

// Format: NIM|Nama|Kelas|Tingkat|Timestamp untuk keamanan
$timestamp = time();
$dataQRCode = "$nim|$nama|$kelas|$tingkat|$timestamp";
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-qrcode"></i> Generate QR Code</h2>
        <p>Generate QR Code untuk absensi apel tingkat</p>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-qrcode"></i> QR Code Mahasiswa</h4>
                </div>
                <div class="card-body text-center">
                    <!-- QR Code Container -->
                    <div id="qrcode-container" class="qr-container">
                        <div id="qrcode" class="qr-display">
                            <div class="loading-spinner">
                                <div class="spinner"></div>
                                <p>Generating QR Code...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Student Info -->
                    <div class="student-info">
                        <div class="info-grid">
                            <div class="info-item">
                                <label>NIM</label>
                                <span><?= htmlspecialchars($nim) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Nama</label>
                                <span><?= htmlspecialchars($nama) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Kelas</label>
                                <span><?= htmlspecialchars($kelas) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Tingkat</label>
                                <span><?= htmlspecialchars($tingkat) ?></span>
                            </div>
                        </div>
                        <div class="generated-time">
                            <small><i class="fas fa-clock"></i> Generated: <?= date('d/m/Y H:i:s', $timestamp) ?></small>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button id="btn-print" class="btn btn-success">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <button id="btn-download" class="btn btn-info">
                            <i class="fas fa-download"></i> Download
                        </button>
                        <button id="btn-refresh" class="btn btn-warning">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> Petunjuk Penggunaan</h5>
                </div>
                <div class="card-body">
                    <div class="instruction-grid">
                        <div class="instruction-item">
                            <i class="fas fa-mobile-alt"></i>
                            <span>Tunjukkan QR Code kepada petugas saat apel</span>
                        </div>
                        <div class="instruction-item">
                            <i class="fas fa-eye"></i>
                            <span>Pastikan QR Code dalam kondisi jelas</span>
                        </div>
                        <div class="instruction-item">
                            <i class="fas fa-clock"></i>
                            <span>QR Code berlaku untuk satu sesi apel</span>
                        </div>
                        <div class="instruction-item">
                            <i class="fas fa-camera"></i>
                            <span>Dapat di-scan dari berbagai sudut</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden data for JavaScript -->
<input type="hidden" id="qr-data" value="<?= htmlspecialchars($dataQRCode) ?>">
<input type="hidden" id="student-data" value="<?= htmlspecialchars(json_encode([
    'nim' => $nim,
    'nama' => $nama, 
    'kelas' => $kelas,
    'tingkat' => $tingkat
])) ?>">

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script src="../../public/js/qr-generator.js"></script>

<?php include '../templates/footer.php'; ?>
