<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
    header('Location: ../auth/login.php');
    exit;
}
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-qrcode"></i> Scan Presensi Mahasiswa</h2>
        <p>Scan QR Code mahasiswa untuk mencatat kehadiran apel tingkat</p>
    </div>

    <div class="row">
        <!-- Scanner Panel -->
        <div class="col-md-6">
            <div class="card scanner-card">
                <div class="card-header">
                    <h4><i class="fas fa-camera"></i> Scanner QR Code</h4>
                </div>
                <div class="card-body">
                    <form id="formTingkat" class="tingkat-form">
                        <div class="form-group">
                            <label for="tingkat" class="form-label">
                                <i class="fas fa-layer-group"></i> Pilih Tingkat Apel
                            </label>
                            <select name="tingkat" id="tingkat" class="form-control" required>
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="1">Tingkat 1</option>
                                <option value="2">Tingkat 2</option>
                                <option value="3">Tingkat 3</option>
                                <option value="4">Tingkat 4</option>
                            </select>
                        </div>
                    </form>

                    <div class="scanner-container">
                        <div id="reader" class="qr-reader">
                            <div class="scanner-placeholder">
                                <i class="fas fa-qrcode"></i>
                                <p>Pilih tingkat terlebih dahulu untuk memulai scan</p>
                            </div>
                        </div>
                        
                        <div id="scan-status" class="scan-status" style="display: none;"></div>
                    </div>

                    <div class="scanner-controls">
                        <button type="button" id="refreshScanner" class="btn btn-warning" onclick="refreshScanner()">
                            <i class="fas fa-sync"></i> Refresh Scanner
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Panel -->
        <div class="col-md-6">
            <div class="card form-card">
                <div class="card-header">
                    <h4><i class="fas fa-user-check"></i> Data Mahasiswa</h4>
                </div>
                <div class="card-body">
                    <form id="formPresensi" action="../../controllers/AbsensiController.php" method="POST">
                        <input type="hidden" name="action" value="scan_presensi">
                        <input type="hidden" name="data_barcode" id="data_barcode">
                        <input type="hidden" name="tingkat_input" id="tingkat_input">

                        <div id="student-info" class="student-info" style="display: none;">
                            <div class="student-card">
                                <div class="student-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="student-details">
                                    <h5 id="display-nama">-</h5>
                                    <div class="student-meta">
                                        <span class="nim-badge" id="display-nim">-</span>
                                        <span class="kelas-badge" id="display-kelas">-</span>
                                        <span class="tingkat-badge" id="display-tingkat">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-clock"></i> Status Kehadiran
                            </label>
                            <div class="status-options">
                                <label class="status-option success">
                                    <input type="radio" name="status" value="Tepat Waktu" required>
                                    <div class="status-content">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Tepat Waktu</span>
                                    </div>
                                </label>
                                <label class="status-option warning">
                                    <input type="radio" name="status" value="Terlambat" required>
                                    <div class="status-content">
                                        <i class="fas fa-clock"></i>
                                        <span>Terlambat</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="keterangan" class="form-label">
                                <i class="fas fa-comment"></i> Keterangan (Opsional)
                            </label>
                            <textarea name="keterangan" id="keterangan" rows="3" class="form-control" 
                                      placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                        </div>

                        <button type="submit" id="btn-simpan" class="btn btn-primary" disabled>
                            <i class="fas fa-save"></i> Simpan Presensi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="card stats-card">
        <div class="card-header">
            <h4><i class="fas fa-chart-line"></i> Statistik Presensi Hari Ini</h4>
            <div class="card-actions">
                <span class="last-update">Update: <span id="lastUpdate"><?= date('H:i:s') ?></span></span>
            </div>
        </div>
        <div class="card-body">
            <div class="stats-grid">
                <div class="stat-item success">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="count-tepat-waktu">0</h3>
                        <p>Tepat Waktu</p>
                    </div>
                </div>
                
                <div class="stat-item warning">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="count-terlambat">0</h3>
                        <p>Terlambat</p>
                    </div>
                </div>
                
                <div class="stat-item danger">
                    <div class="stat-icon">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="count-tidak-hadir">0</h3>
                        <p>Tidak Hadir</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-tools"></i> Quick Actions</h4>
        </div>
        <div class="card-body">
            <div class="quick-actions">
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="../admin/laporan_presensi.php" class="btn btn-info">
                    <i class="fas fa-chart-bar"></i> Lihat Laporan
                </a>
                <button onclick="location.reload()" class="btn btn-warning">
                    <i class="fas fa-sync"></i> Refresh Halaman
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.scanner-card,
.form-card {
    height: fit-content;
}

.tingkat-form {
    margin-bottom: 1.5rem;
}

.scanner-container {
    margin-bottom: 1.5rem;
}

.qr-reader {
    width: 100%;
    height: 300px;
    border: 2px dashed var(--border-color);
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--light-color);
    position: relative;
    overflow: hidden;
}

.scanner-placeholder {
    text-align: center;
    color: var(--secondary-color);
}

.scanner-placeholder i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.scanner-placeholder p {
    margin: 0;
    font-size: 0.875rem;
}

.scan-status {
    padding: 1rem;
    border-radius: var(--border-radius);
    margin-top: 1rem;
    font-weight: 500;
}

.scanner-controls {
    text-align: center;
}

.student-info {
    margin-bottom: 1.5rem;
}

.student-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: var(--light-color);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.student-avatar {
    width: 4rem;
    height: 4rem;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1.5rem;
}

.student-details h5 {
    margin: 0 0 0.5rem 0;
    color: var(--dark-color);
    font-size: 1.125rem;
}

.student-meta {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.nim-badge,
.kelas-badge,
.tingkat-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.nim-badge {
    background: var(--primary-color);
    color: var(--white);
}

.kelas-badge {
    background: var(--info-color);
    color: var(--white);
}

.tingkat-badge {
    background: var(--success-color);
    color: var(--white);
}

.status-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.status-option {
    position: relative;
    cursor: pointer;
}

.status-option input {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    margin: 0;
    cursor: pointer;
}

.status-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    transition: var(--transition);
    background: var(--white);
}

.status-option:hover .status-content {
    border-color: var(--primary-color);
    background: var(--light-color);
}

.status-option input:checked + .status-content {
    border-color: var(--primary-color);
    background: var(--primary-color);
    color: var(--white);
}

.status-option.success input:checked + .status-content {
    border-color: var(--success-color);
    background: var(--success-color);
}

.status-option.warning input:checked + .status-content {
    border-color: var(--warning-color);
    background: var(--warning-color);
    color: var(--dark-color);
}

.status-content i {
    font-size: 1.5rem;
}

.status-content span {
    font-weight: 600;
    font-size: 0.875rem;
}

.stats-card {
    margin-top: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.stat-item.success { background: #dcfce7; }
.stat-item.warning { background: #fef3c7; }
.stat-item.danger { background: #fee2e2; }

.stat-item .stat-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: var(--white);
}

.stat-item.success .stat-icon { background: var(--success-color); }
.stat-item.warning .stat-icon { background: var(--warning-color); }
.stat-item.danger .stat-icon { background: var(--danger-color); }

.stat-item .stat-content h3 {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
    color: var(--dark-color);
}

.stat-item .stat-content p {
    margin: 0;
    color: var(--secondary-color);
    font-weight: 500;
    font-size: 0.875rem;
}

.card-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.last-update {
    color: var(--secondary-color);
    font-size: 0.75rem;
}

.quick-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

#btn-simpan:disabled {
    background: var(--secondary-color);
    cursor: not-allowed;
    opacity: 0.6;
}

@media (max-width: 768px) {
    .status-options {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .student-card {
        flex-direction: column;
        text-align: center;
    }
    
    .quick-actions {
        flex-direction: column;
    }
    
    .card-actions {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
}
</style>

<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>
<script src="../../public/js/scanner.js"></script>

<?php include '../templates/footer.php'; ?>
