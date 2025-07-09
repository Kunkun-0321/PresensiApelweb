<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-envelope-open-text"></i> Ajukan Izin/Sakit</h2>
        <p>Ajukan permohonan izin atau sakit untuk apel tingkat</p>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <div>
                <strong>Berhasil!</strong>
                <p>Pengajuan berhasil dikirim dan menunggu verifikasi admin.</p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Error!</strong>
                <p><?php 
                    switch($_GET['error']) {
                        case 'sudah_ajukan': echo 'Anda sudah mengajukan izin/sakit untuk tanggal dan jenis yang sama.'; break;
                        default: echo htmlspecialchars($_GET['error']); break;
                    }
                ?></p>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-paper-plane"></i> Form Pengajuan</h4>
                </div>
                <div class="card-body">
                    <form action="../../controllers/IzinSakitController.php" method="POST" enctype="multipart/form-data" class="izin-form">
                        <input type="hidden" name="action" value="ajukan">

                        <div class="form-group">
                            <label for="tanggal" class="form-label">
                                <i class="fas fa-calendar"></i> Tanggal Apel
                            </label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" required min="<?= date('Y-m-d') ?>">
                            <small class="form-text">Pilih tanggal apel yang akan diikuti</small>
                        </div>

                        <div class="form-group">
                            <label for="jenis" class="form-label">
                                <i class="fas fa-list"></i> Jenis Pengajuan
                            </label>
                            <select name="jenis" id="jenis" class="form-control" required onchange="toggleBuktiRequired()">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Izin">Izin</option>
                                <option value="Sakit">Sakit</option>
                            </select>
                            <small class="form-text">Pilih jenis pengajuan sesuai kondisi Anda</small>
                        </div>

                        <div class="form-group">
                            <label for="alasan" class="form-label">
                                <i class="fas fa-comment"></i> Alasan
                            </label>
                            <textarea name="alasan" id="alasan" rows="4" class="form-control" 
                                      placeholder="Jelaskan alasan izin/sakit Anda dengan detail..." required></textarea>
                            <small class="form-text">Berikan penjelasan yang jelas dan detail</small>
                        </div>

                        <div class="form-group">
                            <label for="bukti" class="form-label">
                                <i class="fas fa-paperclip"></i> Unggah Bukti
                                <span id="bukti-required" class="text-danger" style="display: none;">*</span>
                            </label>
                            <div class="file-upload-area">
                                <input type="file" name="bukti" id="bukti" accept=".pdf,.jpg,.jpeg,.png" class="file-input">
                                <div class="file-upload-content">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Klik untuk memilih file atau drag & drop</p>
                                    <small>PDF, JPG, PNG (Maksimal 5MB)</small>
                                </div>
                                <div class="file-preview" id="filePreview" style="display: none;">
                                    <i class="fas fa-file"></i>
                                    <span class="file-name"></span>
                                    <button type="button" class="remove-file" onclick="removeFile()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text">
                                <span id="bukti-note">Bukti diperlukan untuk pengajuan sakit (surat dokter, resep, dll)</span>
                            </small>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Ajukan Permohonan
                            </button>
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Guidelines -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> Panduan Pengajuan</h5>
                </div>
                <div class="card-body">
                    <div class="guidelines-grid">
                        <div class="guideline-item">
                            <div class="guideline-icon izin">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <div class="guideline-content">
                                <h6>Pengajuan Izin</h6>
                                <ul>
                                    <li>Untuk keperluan pribadi/keluarga</li>
                                    <li>Diajukan minimal H-1</li>
                                    <li>Sertakan alasan yang jelas</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="guideline-item">
                            <div class="guideline-icon sakit">
                                <i class="fas fa-user-injured"></i>
                            </div>
                            <div class="guideline-content">
                                <h6>Pengajuan Sakit</h6>
                                <ul>
                                    <li>Untuk kondisi kesehatan</li>
                                    <li>Wajib melampirkan surat dokter</li>
                                    <li>Dapat diajukan pada hari yang sama</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="important-note">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <strong>Penting:</strong>
                            <p>Pengajuan akan diverifikasi oleh admin. Pastikan semua informasi yang diberikan akurat dan lengkap.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.alert {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-radius: var(--border-radius);
    margin-bottom: 1.5rem;
}

.alert i {
    font-size: 1.25rem;
    margin-top: 0.125rem;
}

.alert-success {
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    color: #166534;
}

.alert-danger {
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.alert div strong {
    display: block;
    margin-bottom: 0.25rem;
}

.alert div p {
    margin: 0;
    font-size: 0.875rem;
}

.izin-form {
    max-width: none;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
}

.form-text {
    color: var(--secondary-color);
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.file-upload-area {
    position: relative;
    border: 2px dashed var(--border-color);
    border-radius: var(--border-radius);
    padding: 2rem;
    text-align: center;
    transition: var(--transition);
    cursor: pointer;
}

.file-upload-area:hover {
    border-color: var(--primary-color);
    background: var(--light-color);
}

.file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.file-upload-content i {
    font-size: 2rem;
    color: var(--secondary-color);
    margin-bottom: 0.5rem;
}

.file-upload-content p {
    margin: 0 0 0.25rem 0;
    color: var(--dark-color);
    font-weight: 500;
}

.file-upload-content small {
    color: var(--secondary-color);
}

.file-preview {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--light-color);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.file-preview i {
    color: var(--primary-color);
    font-size: 1.25rem;
}

.file-name {
    flex: 1;
    font-weight: 500;
    color: var(--dark-color);
}

.remove-file {
    background: var(--danger-color);
    color: var(--white);
    border: none;
    border-radius: 50%;
    width: 1.5rem;
    height: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.75rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
}

.guidelines-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.guideline-item {
    display: flex;
    gap: 1rem;
    padding: 1.5rem;
    background: var(--light-color);
    border-radius: var(--border-radius);
}

.guideline-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1.25rem;
    flex-shrink: 0;
}

.guideline-icon.izin { background: var(--info-color); }
.guideline-icon.sakit { background: var(--danger-color); }

.guideline-content h6 {
    margin: 0 0 0.5rem 0;
    color: var(--dark-color);
    font-weight: 600;
}

.guideline-content ul {
    margin: 0;
    padding-left: 1rem;
    color: var(--secondary-color);
    font-size: 0.875rem;
}

.guideline-content li {
    margin-bottom: 0.25rem;
}

.important-note {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-radius: var(--border-radius);
    color: #92400e;
}

.important-note i {
    font-size: 1.25rem;
    margin-top: 0.125rem;
}

.important-note strong {
    display: block;
    margin-bottom: 0.25rem;
}

.important-note p {
    margin: 0;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .guidelines-grid {
        grid-template-columns: 1fr;
    }
    
    .guideline-item {
        flex-direction: column;
        text-align: center;
    }
    
    .important-note {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script>
function toggleBuktiRequired() {
    const jenis = document.getElementById('jenis').value;
    const buktiRequired = document.getElementById('bukti-required');
    const buktiNote = document.getElementById('bukti-note');
    const buktiInput = document.getElementById('bukti');
    
    if (jenis === 'Sakit') {
        buktiRequired.style.display = 'inline';
        buktiInput.required = true;
        buktiNote.textContent = 'Wajib melampirkan surat dokter atau bukti medis lainnya';
    } else {
        buktiRequired.style.display = 'none';
        buktiInput.required = false;
        buktiNote.textContent = 'Bukti pendukung (opsional untuk izin, wajib untuk sakit)';
    }
}

document.getElementById('bukti').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const uploadContent = document.querySelector('.file-upload-content');
    const filePreview = document.getElementById('filePreview');
    
    if (file) {
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 5MB.');
            e.target.value = '';
            return;
        }
        
        // Show preview
        uploadContent.style.display = 'none';
        filePreview.style.display = 'flex';
        filePreview.querySelector('.file-name').textContent = file.name;
    }
});

function removeFile() {
    const buktiInput = document.getElementById('bukti');
    const uploadContent = document.querySelector('.file-upload-content');
    const filePreview = document.getElementById('filePreview');
    
    buktiInput.value = '';
    uploadContent.style.display = 'block';
    filePreview.style.display = 'none';
}

// Drag and drop functionality
const uploadArea = document.querySelector('.file-upload-area');

uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    uploadArea.style.borderColor = 'var(--primary-color)';
    uploadArea.style.background = 'var(--light-color)';
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    uploadArea.style.borderColor = 'var(--border-color)';
    uploadArea.style.background = 'transparent';
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    uploadArea.style.borderColor = 'var(--border-color)';
    uploadArea.style.background = 'transparent';
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('bukti').files = files;
        document.getElementById('bukti').dispatchEvent(new Event('change'));
    }
});
</script>

<?php include '../templates/footer.php'; ?>
