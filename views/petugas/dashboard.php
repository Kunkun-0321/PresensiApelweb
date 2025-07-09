<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Absensi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
    header('Location: ../auth/login.php');
    exit;
}

// Get today's statistics
$today = date('Y-m-d');
$stats = getStatistikHarian(Database::getConnection(), $today);
$total_mahasiswa = getTotalMahasiswa(Database::getConnection());
$tidak_hadir = $total_mahasiswa - ($stats['total_hadir'] ?? 0);

// Get recent attendance
$recent_attendance = Absensi::getPresensiTerbaru(5, $today);
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-user-tie"></i> Dashboard Petugas</h2>
        <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['user']['nama'] ?? $_SESSION['user']['username']) ?></strong></p>
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
                <h3><?= $stats['total_hadir'] ?? 0 ?></h3>
                <p>Hadir Hari Ini</p>
            </div>
        </div>
        
        <div class="stat-card stat-warning">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?= $stats['terlambat'] ?? 0 ?></h3>
                <p>Terlambat</p>
            </div>
        </div>
        
        <div class="stat-card stat-danger">
            <div class="stat-icon">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="stat-content">
                <h3><?= $tidak_hadir ?></h3>
                <p>Tidak Hadir</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-6">
            <div class="card action-card primary">
                <div class="card-body">
                    <div class="action-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div class="action-content">
                        <h4>Scan Presensi</h4>
                        <p>Scan QR Code mahasiswa untuk mencatat kehadiran</p>
                        <a href="scan_presensi.php" class="btn btn-primary">
                            <i class="fas fa-camera"></i> Mulai Scan
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card action-card info">
                <div class="card-body">
                    <div class="action-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="action-content">
                        <h4>Lihat Laporan</h4>
                        <p>Lihat laporan presensi dan statistik kehadiran</p>
                        <a href="../admin/laporan_presensi.php" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-clock"></i> Presensi Terbaru Hari Ini</h4>
            <div class="card-actions">
                <span class="last-update">Update: <?= date('H:i:s') ?></span>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($recent_attendance)): ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>Belum Ada Presensi</h3>
                    <p>Belum ada mahasiswa yang melakukan presensi hari ini</p>
                    <a href="scan_presensi.php" class="btn btn-primary">
                        <i class="fas fa-qrcode"></i> Mulai Scan Presensi
                    </a>
                </div>
            <?php else: ?>
                <div class="attendance-list">
                    <?php foreach ($recent_attendance as $row): ?>
                    <div class="attendance-item">
                        <div class="attendance-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="attendance-info">
                            <h5><?= htmlspecialchars($row['nama']) ?></h5>
                            <span class="nim-badge"><?= htmlspecialchars($row['nim']) ?></span>
                        </div>
                        <div class="attendance-status">
                            <span class="status-badge <?= $row['status'] === 'Tepat Waktu' ? 'success' : 'warning' ?>">
                                <i class="fas fa-<?= $row['status'] === 'Tepat Waktu' ? 'check' : 'clock' ?>"></i>
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </div>
                        <div class="attendance-time">
                            <span class="time-badge">
                                <i class="fas fa-clock"></i>
                                <?= date('H:i', strtotime($row['waktu'])) ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="card-footer-custom">
                    <a href="scan_presensi.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Presensi
                    </a>
                    <a href="../admin/laporan_presensi.php" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> Lihat Semua
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
.stat-danger .stat-icon { background: var(--danger-color); }

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

.action-card {
    transition: var(--transition);
}

.action-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.action-card .card-body {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.action-icon {
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--white);
    flex-shrink: 0;
}

.action-card.primary .action-icon { background: var(--primary-color); }
.action-card.info .action-icon { background: var(--info-color); }

.action-content h4 {
    margin: 0 0 0.5rem 0;
    color: var(--dark-color);
}

.action-content p {
    margin: 0 0 1rem 0;
    color: var(--secondary-color);
    font-size: 0.875rem;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.last-update {
    color: var(--secondary-color);
    font-size: 0.75rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--secondary-color);
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h3 {
    margin-bottom: 0.5rem;
    color: var(--dark-color);
}

.empty-state p {
    margin-bottom: 2rem;
}

.attendance-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.attendance-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--light-color);
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.attendance-item:hover {
    background: #e2e8f0;
}

.attendance-avatar {
    width: 3rem;
    height: 3rem;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1.25rem;
}

.attendance-info {
    flex: 1;
}

.attendance-info h5 {
    margin: 0 0 0.25rem 0;
    color: var(--dark-color);
    font-size: 1rem;
}

.nim-badge {
    background: var(--white);
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--secondary-color);
}

.status-badge {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--white);
}

.status-badge.success { background: var(--success-color); }
.status-badge.warning { background: var(--warning-color); }

.time-badge {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    color: var(--secondary-color);
    font-size: 0.875rem;
}

.card-footer-custom {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn-outline-primary {
    background: transparent;
    border: 1px solid var(--primary-color);
    color: var(--primary-color);
}

.btn-outline-primary:hover {
    background: var(--primary-color);
    color: var(--white);
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .action-card .card-body {
        flex-direction: column;
        text-align: center;
    }
    
    .attendance-item {
        flex-direction: column;
        text-align: center;
    }
    
    .card-footer-custom {
        flex-direction: column;
    }
}
</style>

<?php include '../templates/footer.php'; ?>
