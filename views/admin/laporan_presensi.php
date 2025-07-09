<?php
session_start();
require_once '../../config/Database.php';
require_once '../../models/Absensi.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'petugas')) {
    header('Location: ../auth/login.php');
    exit;
}

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$tingkat = $_GET['tingkat'] ?? '';
$laporan = Absensi::getLaporanPresensi($tanggal, $tingkat);

// Get statistics
$stats = [
    'total' => count($laporan),
    'tepat_waktu' => count(array_filter($laporan, fn($item) => $item['status'] === 'Tepat Waktu')),
    'terlambat' => count(array_filter($laporan, fn($item) => $item['status'] === 'Terlambat'))
];
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-chart-bar"></i> Laporan Presensi</h2>
        <p>Laporan kehadiran mahasiswa pada apel tingkat</p>
    </div>

    <!-- Filter Section -->
    <div class="card filter-card">
        <div class="card-header">
            <h4><i class="fas fa-filter"></i> Filter Laporan</h4>
        </div>
        <div class="card-body">
            <form method="GET" class="filter-form">
                <div class="filter-grid">
                    <div class="filter-item">
                        <label for="tanggal" class="form-label">
                            <i class="fas fa-calendar"></i> Tanggal Apel
                        </label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" 
                               value="<?= htmlspecialchars($tanggal) ?>" required>
                    </div>

                    <div class="filter-item">
                        <label for="tingkat" class="form-label">
                            <i class="fas fa-layer-group"></i> Tingkat
                        </label>
                        <select name="tingkat" id="tingkat" class="form-control">
                            <option value="">Semua Tingkat</option>
                            <option value="1" <?= $tingkat==='1'?'selected':'' ?>>Tingkat 1</option>
                            <option value="2" <?= $tingkat==='2'?'selected':'' ?>>Tingkat 2</option>
                            <option value="3" <?= $tingkat==='3'?'selected':'' ?>>Tingkat 3</option>
                            <option value="4" <?= $tingkat==='4'?'selected':'' ?>>Tingkat 4</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <label class="form-label" style="opacity: 0;">Action</label>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <?php if (!empty($laporan)): ?>
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3><?= $stats['total'] ?></h3>
                <p>Total Hadir</p>
            </div>
        </div>
        
        <div class="stat-card stat-success">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?= $stats['tepat_waktu'] ?></h3>
                <p>Tepat Waktu</p>
            </div>
        </div>
        
        <div class="stat-card stat-warning">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?= $stats['terlambat'] ?></h3>
                <p>Terlambat</p>
            </div>
        </div>
        
        <div class="stat-card stat-info">
            <div class="stat-icon">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-content">
                <h3><?= $stats['total'] > 0 ? round(($stats['tepat_waktu'] / $stats['total']) * 100) : 0 ?>%</h3>
                <p>Ketepatan</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Report Table -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-table"></i> Data Presensi</h4>
            <div class="card-actions">
                <?php if (!empty($laporan)): ?>
                <button onclick="exportToCSV()" class="btn btn-success btn-sm">
                    <i class="fas fa-download"></i> Export CSV
                </button>
                <button onclick="printReport()" class="btn btn-info btn-sm">
                    <i class="fas fa-print"></i> Print
                </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($laporan)): ?>
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>Tidak Ada Data</h3>
                    <p>Tidak ada data presensi untuk filter yang dipilih</p>
                    <small>Coba ubah tanggal atau tingkat untuk melihat data lainnya</small>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table" id="reportTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Tingkat</th>
                                <th>Waktu</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($laporan as $index => $row): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <span class="nim-badge"><?= htmlspecialchars($row['nim']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($row['kelas']) ?></td>
                                <td>
                                    <span class="tingkat-badge tingkat-<?= $row['tingkat'] ?>">
                                        <?= htmlspecialchars($row['tingkat']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="time-badge">
                                        <i class="fas fa-clock"></i>
                                        <?= date('H:i', strtotime($row['waktu'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?= $row['status'] === 'Tepat Waktu' ? 'success' : 'warning' ?>">
                                        <i class="fas fa-<?= $row['status'] === 'Tepat Waktu' ? 'check' : 'clock' ?>"></i>
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['keterangan'] ?? '-') ?>
                                </td>
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
.filter-card {
    margin-bottom: 2rem;
}

.filter-form {
    margin: 0;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

.filter-item {
    display: flex;
    flex-direction: column;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
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
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: var(--white);
}

.stat-primary .stat-icon { background: var(--primary-color); }
.stat-success .stat-icon { background: var(--success-color); }
.stat-warning .stat-icon { background: var(--warning-color); }
.stat-info .stat-icon { background: var(--info-color); }

.stat-content h3 {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
    color: var(--dark-color);
}

.stat-content p {
    margin: 0;
    color: var(--secondary-color);
    font-weight: 500;
    font-size: 0.875rem;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.5rem 1rem;
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

.nim-badge {
    background: var(--light-color);
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-family: monospace;
    font-weight: 600;
    color: var(--dark-color);
    font-size: 0.75rem;
}

.tingkat-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--white);
}

.tingkat-1 { background: #3b82f6; }
.tingkat-2 { background: #10b981; }
.tingkat-3 { background: #f59e0b; }
.tingkat-4 { background: #ef4444; }

.time-badge {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
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

@media (max-width: 768px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .card-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .card-actions {
        justify-content: center;
    }
}
</style>

<script>
function exportToCSV() {
    const table = document.getElementById('reportTable');
    const rows = table.querySelectorAll('tr');
    let csv = [];

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cols = row.querySelectorAll('td, th');
        let csvRow = [];
        
        for (let j = 0; j < cols.length; j++) {
            csvRow.push('"' + cols[j].textContent.trim() + '"');
        }
        
        csv.push(csvRow.join(','));
    }

    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    
    a.href = url;
    a.download = `laporan_presensi_${new Date().toISOString().slice(0, 10)}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

function printReport() {
    window.print();
}
</script>

<?php include '../templates/footer.php'; ?>
