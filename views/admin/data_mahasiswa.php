<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/User.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$mahasiswaList = User::getAllMahasiswa();
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-users"></i> Data Mahasiswa</h2>
        <p>Kelola data mahasiswa yang terdaftar dalam sistem</p>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="action-left">
            <a href="import_mahasiswa.php" class="btn btn-success">
                <i class="fas fa-file-import"></i> Import CSV
            </a>
            <button class="btn btn-primary" onclick="showAddModal()">
                <i class="fas fa-plus"></i> Tambah Mahasiswa
            </button>
        </div>
        <div class="action-right">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari mahasiswa..." onkeyup="searchTable()">
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-table"></i> Daftar Mahasiswa</h4>
            <div class="card-actions">
                <span class="record-count">Total: <?= count($mahasiswaList) ?> mahasiswa</span>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($mahasiswaList)): ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>Belum Ada Data Mahasiswa</h3>
                    <p>Mulai dengan menambah mahasiswa baru atau import dari file CSV</p>
                    <a href="import_mahasiswa.php" class="btn btn-primary">
                        <i class="fas fa-file-import"></i> Import CSV
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table" id="mahasiswaTable">
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Tingkat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mahasiswaList as $mhs): ?>
                            <tr>
                                <td>
                                    <span class="nim-badge"><?= htmlspecialchars($mhs['nim']) ?></span>
                                </td>
                                <td>
                                    <div class="student-info">
                                        <strong><?= htmlspecialchars($mhs['nama']) ?></strong>
                                        <small>@<?= htmlspecialchars($mhs['username']) ?></small>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($mhs['kelas']) ?></td>
                                <td>
                                    <span class="tingkat-badge tingkat-<?= $mhs['tingkat'] ?>">
                                        Tingkat <?= htmlspecialchars($mhs['tingkat']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge active">
                                        <i class="fas fa-circle"></i> Aktif
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action edit" onclick="editMahasiswa(<?= $mhs['id'] ?>)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action delete" onclick="deleteMahasiswa(<?= $mhs['id'] ?>, '<?= htmlspecialchars($mhs['nama']) ?>')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
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
.action-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    gap: 1rem;
}

.action-left {
    display: flex;
    gap: 0.75rem;
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-box i {
    position: absolute;
    left: 1rem;
    color: var(--secondary-color);
}

.search-box input {
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    width: 250px;
    font-size: 0.875rem;
}

.search-box input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.record-count {
    color: var(--secondary-color);
    font-size: 0.875rem;
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
}

.student-info strong {
    display: block;
    color: var(--dark-color);
}

.student-info small {
    color: var(--secondary-color);
}

.tingkat-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--white);
}

.tingkat-1 { background: #3b82f6; }
.tingkat-2 { background: #10b981; }
.tingkat-3 { background: #f59e0b; }
.tingkat-4 { background: #ef4444; }

.status-badge {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.active {
    color: var(--success-color);
}

.status-badge i {
    font-size: 0.5rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-action {
    width: 2rem;
    height: 2rem;
    border: none;
    border-radius: 0.25rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    font-size: 0.75rem;
}

.btn-action.edit {
    background: #dbeafe;
    color: #1d4ed8;
}

.btn-action.edit:hover {
    background: #bfdbfe;
}

.btn-action.delete {
    background: #fee2e2;
    color: #dc2626;
}

.btn-action.delete:hover {
    background: #fecaca;
}

@media (max-width: 768px) {
    .action-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .action-left {
        justify-content: center;
    }
    
    .search-box input {
        width: 100%;
    }
    
    .card-header {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
    
    .student-info {
        font-size: 0.875rem;
    }
    
    .action-buttons {
        justify-content: center;
    }
}
</style>

<script>
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('mahasiswaTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;

        for (let j = 0; j < cells.length - 1; j++) {
            if (cells[j].textContent.toLowerCase().includes(filter)) {
                found = true;
                break;
            }
        }

        row.style.display = found ? '' : 'none';
    }
}

function editMahasiswa(id) {
    // Implement edit functionality
    alert('Edit mahasiswa ID: ' + id);
}

function deleteMahasiswa(id, nama) {
    if (confirm('Yakin ingin menghapus mahasiswa ' + nama + '?')) {
        // Implement delete functionality
        window.location.href = '../../controllers/MahasiswaController.php?hapus=' + id;
    }
}

function showAddModal() {
    // Implement add modal
    alert('Fitur tambah mahasiswa akan segera tersedia');
}
</script>

<?php include '../templates/footer.php'; ?>
