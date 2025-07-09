<?php include_once '../templates/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3><i class="fas fa-sign-in-alt"></i> Login</h3>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Login gagal. Cek kembali username & password.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['logout'])): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Anda telah berhasil logout.
                    </div>
                <?php endif; ?>

                <form action="../../controllers/AuthController.php" method="POST">
                    <input type="hidden" name="action" value="login">

                    <div class="form-group">
                        <label for="username" class="form-label">
                            <i class="fas fa-user"></i> Username:
                        </label>
                        <input type="text" name="username" class="form-control" required placeholder="Masukkan username">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password:
                        </label>
                        <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-arrow-right-to-bracket"></i> Login
                    </button>
                </form>

                <div class="card-footer">
                    <small class="text-muted">
                        <strong>Demo Accounts:</strong><br>
                        Admin: admin001 / admin123<br>
                        Petugas: petugas001 / admin123<br>
                        Mahasiswa: 2024001 / admin123
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../templates/footer.php'; ?>
