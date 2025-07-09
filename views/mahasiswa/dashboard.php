<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

$nim = $_SESSION['user']['username'];
$conn = Database::getConnection();

$riwayatPresensi = getRiwayatPresensiMahasiswa($conn, $nim);
$statusIzin = getStatusIzinSakitMahasiswa($conn, $nim);
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-user-graduate"></i> Dashboard Mahasiswa</h2>
        <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['user']['nama'] ?? $_SESSION['user']['username']) ?></strong></p>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-qrcode" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                    <h4>Generate QR Code</h4>
                    <p>Buat QR Code untuk presensi apel tingkat</p>
                    <a href="generate_barcode.php" class="btn btn-primary">
                        <i class="fas fa-qrcode"></i> Generate QR Code
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-envelope-open-text" style="font-size: 3rem; color: var(--warning-color); margin-bottom: 1rem;"></i>
                    <h4>Ajukan Izin</h4>
                    <p>Ajukan izin atau sakit untuk apel tingkat</p>
                    <a href="ajukan_izin.php" class="btn btn-warning">
                        <i class="fas fa-paper-plane"></i> Ajukan Izin
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Presensi -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-history"></i> Riwayat Presensi</h4>
        </div>
        <div class="card-body">
            <?php if (empty($riwayatPresensi)): ?>
                <div class="text-center" style="padding: 3rem; color: var(--secondary-color);">
                    <i class="fas fa-info-circle" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <p>Belum ada riwayat presensi</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayatPresensi as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['tanggal']) ?></td>
                                <td><?= htmlspecialchars($r['waktu']) ?></td>
                                <td>
                                    <span class="badge <?= $r['status'] === 'Tepat Waktu' ? 'badge-success' : 'badge-warning' ?>">
                                        <?= htmlspecialchars($r['status']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($r['keterangan'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status Izin/Sakit -->
    <div class="card mt-4">
        <div class="card-header">
            <h4><i class="fas fa-file-medical"></i> Status Izin/Sakit</h4>
        </div>
        <div class="card-body">
            <?php if (empty($statusIzin)): ?>
                <div class="text-center" style="padding: 3rem; color: var(--secondary-color);">
                    <i class="fas fa-info-circle" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <p>Belum ada pengajuan izin/sakit</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Status</th>
                                <th>Alasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($statusIzin as $izin): ?>
                            <tr>
                                <td><?= htmlspecialchars($izin['tanggal']) ?></td>
                                <td><?= htmlspecialchars($izin['jenis']) ?></td>
                                <td>
                                    <span class="badge <?php 
                                        switch($izin['status']) {
                                            case 'diterima': echo 'badge-success'; break;
                                            case 'ditolak': echo 'badge-danger'; break;
                                            default: echo 'badge-warning'; break;
                                        }
                                    ?>">
                                        <?= ucfirst(htmlspecialchars($izin['status'])) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($izin['alasan']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.badge {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 0.375rem;
}

.badge-success {
    background-color: #dcfce7;
    color: #166534;
}

.badge-warning {
    background-color: #fef3c7;
    color: #92400e;
}

.badge-danger {
    background-color: #fee2e2;
    color: #991b1b;
}

.table-responsive {
    overflow-x: auto;
}
</style>

<?php include '../templates/footer.php'; ?>
