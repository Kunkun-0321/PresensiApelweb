<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/IzinSakit.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$pengajuan = IzinSakit::getPengajuanBelumVerif();
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-check-square"></i> Verifikasi Izin/Sakit</h2>
        <p>Verifikasi pengajuan izin dan sakit mahasiswa</p>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-item">
            <div class="stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?= count($pengajuan) ?></h3>
                <p>Menunggu Verifikasi</p>
            </div>
        </div>
    </div>

    <!-- Verification Cards -->
    <div class="verification-container">
        <?php if (empty($pengajuan)): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>Semua Pengajuan Sudah Diverifikasi</h3>
                <p>Tidak ada pengajuan izin atau sakit yang menunggu verifikasi</p>
                <a href="dashboard.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($pengajuan as $data): ?>
            <div class="verification-card">
                <div class="card-header-custom">
                    <div class="student-info">
                        <h4><?= htmlspecialchars($data['nama']) ?></h4>
                        <span class="nim-badge"><?= htmlspecialchars($data['nim']) ?></span>
                    </div>
                    <div class="request-type">
                        <span class="type-badge <?= strtolower($data['jenis']) ?>">
                            <i class="fas fa-<?= $data['jenis'] === 'Sakit' ? 'user-injured' : 'calendar-times' ?>"></i>
                            <?= htmlspecialchars($data['jenis']) ?>
                        </span>
                    </div>
                </div>
                
                <div class="card-content">
                    <div class="info-grid">
                        <div class="info-item">
                            <label><i class="fas fa-calendar"></i> Tanggal</label>
                            <span><?= date('d/m/Y', strtotime($data['tanggal'])) ?></span>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-clock"></i> Diajukan</label>
                            <span><?= date('d/m/Y H:i', strtotime($data['created_at'] ?? $data['tanggal'])) ?></span>
                        </div>
                    </div>
                    
                    <div class="reason-section">
                        <label><i class="fas fa-comment"></i> Alasan</label>
                        <p><?= htmlspecialchars($data['alasan']) ?></p>
                    </div>
                    
                    <?php if ($data['file_bukti']): ?>
                    <div class="evidence-section">
                        <label><i class="fas fa-paperclip"></i> Bukti</label>
                        <a href="../../uploads/bukti_izin/<?= htmlspecialchars($data['file_bukti']) ?>" 
                           target="_blank" class="evidence-link">
                            <i class="fas fa-eye"></i> Lihat Bukti
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-actions">
                    <form action="../../controllers/IzinSakitController.php" method="POST" class="verification-form">
                        <input type="hidden" name="action" value="verifikasi">
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">
                        
                        <button type="submit" name="status" value="diterima" class="btn btn-success">
                            <i class="fas fa-check"></i> Terima
                        </button>
                        <button type="submit" name="status" value="ditolak" class="btn btn-danger" 
                                onclick="return confirm('Yakin tolak pengajuan ini?')">
                            <i class="fas fa-times"></i> Tolak
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.stats-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-item {
    background: var(--white);
    padding: 1.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1.25rem;
}

.stat-icon.pending { background: var(--warning-color); }

.stat-info h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    color: var(--dark-color);
}

.stat-info p {
    margin: 0;
    color: var(--secondary-color);
    font-size: 0.875rem;
}

.verification-container {
    display: grid;
    gap: 1.5rem;
}

.verification-card {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: var(--transition);
}

.verification-card:hover {
    box-shadow: var(--shadow-lg);
}

.card-header-custom {
    background: var(--light-color);
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border-color);
}

.student-info h4 {
    margin: 0 0 0.25rem 0;
    color: var(--dark-color);
}

.nim-badge {
    background: var(--primary-color);
    color: var(--white);
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.type-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 1rem;
    font-weight: 600;
    color: var(--white);
}

.type-badge.sakit { background: var(--danger-color); }
.type-badge.izin { background: var(--info-color); }

.card-content {
    padding: 1.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.info-item label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--secondary-color);
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.info-item span {
    color: var(--dark-color);
    font-weight: 500;
}

.reason-section,
.evidence-section {
    margin-bottom: 1.5rem;
}

.reason-section label,
.evidence-section label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--secondary-color);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.reason-section p {
    background: var(--light-color);
    padding: 1rem;
    border-radius: var(--border-radius);
    margin: 0;
    color: var(--dark-color);
    line-height: 1.6;
}

.evidence-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: var(--info-color);
    color: var(--white);
    text-decoration: none;
    border-radius: var(--border-radius);
    font-size: 0.875rem;
    font-weight: 500;
    transition: var(--transition);
}

.evidence-link:hover {
    background: #0e7490;
}

.card-actions {
    background: var(--light-color);
    padding: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.verification-form {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.empty-state i {
    font-size: 4rem;
    color: var(--success-color);
    margin-bottom: 1rem;
}

.empty-state h3 {
    margin-bottom: 0.5rem;
    color: var(--dark-color);
}

.empty-state p {
    color: var(--secondary-color);
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .card-header-custom {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .verification-form {
        flex-direction: column;
    }
    
    .verification-form .btn {
        width: 100%;
    }
}
</style>

<?php include '../templates/footer.php'; ?>
