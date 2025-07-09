<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Get quick statistics
$conn = Database::getConnection();
$total_mahasiswa = getTotalMahasiswa($conn);
$today = date('Y-m-d');
$stats_today = getStatistikHarian($conn, $today);

// Get pending verifications
$stmt = $conn->query("SELECT COUNT(*) FROM izin_sakit WHERE status_verifikasi = 'menunggu'");
$pending_verifikasi = $stmt->fetchColumn();
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-user-shield"></i> Dashboard Admin</h2>
        <p>Panel administrasi sistem absensi apel tingkat</p>
    </div>

    <!-- Quick Stats -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3><?= $total_mahasiswa ?></h3>
                <p>Total Mahasiswa</p>
            </div>
        </div>
        
        <div class="stat-card stat-success">
            <div class="stat-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <h3><?= $stats_today['total_hadir'] ?? 0 ?></h3>
                <p>Hadir Hari Ini</p>
            </div>
        </div>
        
        <div class="stat-card stat-warning">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?= $pending_verifikasi ?></h3>
                <p>Menunggu Verifikasi</p>
            </div>
        </div>
        
        <div class="stat-card stat-info">
            <div class="stat-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
                <h3><?= date('d') ?></h3>
                <p><?= date('F Y') ?></p>
            </div>
        </div>
    </div>

    <!-- Main Actions -->
    <div class="row">
        <div class="col-md-6">
            <div class="card action-card">
                <div class="card-body">
                    <div class="action-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="action-content">
                        <h4>Kelola Data Mahasiswa</h4>
                        <p>Tambah, edit, atau hapus data mahasiswa dalam sistem</p>
                        <a href="data_mahasiswa.php" class="btn btn-primary">
                            <i class="fas fa-users"></i> Kelola Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card action-card">
                <div class="card-body">
                    <div class="action-icon">
                        <i class="fas fa-file-import"></i>
                    </div>
                    <div class="action-content">
                        <h4>Import Mahasiswa</h4>
                        <p>Import data mahasiswa dari file CSV</p>
                        <a href="import_mahasiswa.php" class="btn btn-success">
                            <i class="fas fa-file-import"></i> Import CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card action-card">
                <div class="card-body">
                    <div class="action-icon">
                        <i class="fas fa-check-square"></i>
                    </div>
                    <div class="action-content">
                        <h4>Verifikasi Izin/Sakit</h4>
                        <p>Verifikasi pengajuan izin dan sakit mahasiswa</p>
                        <a href="verifikasi_izin.php" class="btn btn-warning">
                            <i class="fas fa-check-square"></i> Verifikasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card action-card">
                <div class="card-body">
                    <div class="action-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="action-content">
                        <h4>Laporan Presensi</h4>
                        <p>Lihat dan export laporan presensi mahasiswa</p>
                        <a href="laporan_presensi.php" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-clock"></i> Aktivitas Terbaru</h4>
        </div>
        <div class="card-body">
            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-icon success">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="activity-content">
                        <p><strong>Sistem dimulai</strong></p>
                        <small>Dashboard admin siap digunakan</small>
                    </div>
                    <div class="activity-time">
                        <small><?= date('H:i') ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.stat-icon {
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--white);
}

.stat-primary .stat-icon { background: var(--primary-color); }
.stat-success .stat-icon { background: var(--success-color); }
.stat-warning .stat-icon { background: var(--warning-color); }
.stat-info .stat-icon { background: var(--info-color); }

.stat-content h3 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: var(--dark-color);
}

.stat-content p {
    margin: 0;
    color: var(--secondary-color);
    font-weight: 500;
}

.action-card .card-body {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.action-icon {
    width: 4rem;
    height: 4rem;
    background: var(--light-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--primary-color);
    flex-shrink: 0;
}

.action-content h4 {
    margin: 0 0 0.5rem 0;
    color: var(--dark-color);
}

.action-content p {
    margin: 0 0 1rem 0;
    color: var(--secondary-color);
    font-size: 0.875rem;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--light-color);
    border-radius: var(--border-radius);
}

.activity-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 0.875rem;
}

.activity-icon.success { background: var(--success-color); }

.activity-content {
    flex: 1;
}

.activity-content p {
    margin: 0;
    font-size: 0.875rem;
}

.activity-content small {
    color: var(--secondary-color);
}

.activity-time {
    color: var(--secondary-color);
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .action-card .card-body {
        flex-direction: column;
        text-align: center;
    }
    
    .activity-item {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<?php include '../templates/footer.php'; ?>
