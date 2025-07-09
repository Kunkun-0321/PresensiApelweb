<?php
$base_path = getBasePath();

if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'];
    $username = $_SESSION['user']['username'];
    $nama = $_SESSION['user']['nama'] ?? $username;
?>
<nav class="main-nav">
    <div class="container">
        <div class="nav-content">
            <div class="nav-links">
                <a href="<?php 
                    switch($role) {
                        case 'admin': echo $base_path . 'views/admin/dashboard.php'; break;
                        case 'petugas': echo $base_path . 'views/petugas/dashboard.php'; break;
                        case 'mahasiswa': echo $base_path . 'views/mahasiswa/dashboard.php'; break;
                    }
                ?>" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                
                <?php if ($role === 'admin'): ?>
                    <a href="<?= $base_path ?>views/admin/data_mahasiswa.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Data Mahasiswa</span>
                    </a>
                    <a href="<?= $base_path ?>views/admin/laporan_presensi.php" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Laporan</span>
                    </a>
                    <a href="<?= $base_path ?>views/admin/verifikasi_izin.php" class="nav-link">
                        <i class="fas fa-check-square"></i>
                        <span>Verifikasi Izin</span>
                    </a>
                <?php elseif ($role === 'petugas'): ?>
                    <a href="<?= $base_path ?>views/petugas/scan_presensi.php" class="nav-link">
                        <i class="fas fa-qrcode"></i>
                        <span>Scan Presensi</span>
                    </a>
                    <a href="<?= $base_path ?>views/admin/laporan_presensi.php" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Laporan</span>
                    </a>
                <?php elseif ($role === 'mahasiswa'): ?>
                    <a href="<?= $base_path ?>views/mahasiswa/generate_barcode.php" class="nav-link">
                        <i class="fas fa-qrcode"></i>
                        <span>Generate QR Code</span>
                    </a>
                    <a href="<?= $base_path ?>views/mahasiswa/ajukan_izin.php" class="nav-link">
                        <i class="fas fa-envelope-open-text"></i>
                        <span>Ajukan Izin</span>
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="nav-user">
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <div class="user-details">
                        <span class="user-name"><?= htmlspecialchars($nama) ?></span>
                        <small class="user-role"><?= ucfirst($role) ?></small>
                    </div>
                </div>
                <a href="<?= $base_path ?>controllers/AuthController.php?logout=1" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</nav>
<?php } ?>
