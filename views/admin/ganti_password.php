<?php include '../templates/header.php'; ?>
<?php include '../templates/navbar.php'; ?>

<style>
  .container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 60px;
  }

  .card {
    background: #fff;
    padding: 30px;
    width: 100%;
    max-width: 420px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .card-header h3 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
  }

  .alert {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
    text-align: center;
    font-weight: bold;
  }

  .alert-danger {
    background-color: #ffe5e5;
    color: #c00;
  }

  .alert-success {
    background-color: #e0fce0;
    color: #006600;
  }

  .form-group {
    margin-bottom: 15px;
  }

  .form-group label {
    font-weight: 500;
    display: block;
    margin-bottom: 5px;
  }

  .form-control {
    width: 100%;
    padding: 10px;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
  }

  .btn-submit {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: white;
    font-size: 1rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  .btn-submit:hover {
    background-color: #0056b3;
  }
</style>

<div class="container">
  <div class="card">
    <div class="card-header">
      <h3><i class="fas fa-key"></i> Ganti Password</h3>
    </div>
    <div class="card-body">
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">Gagal mengganti password. Cek kembali.</div>
      <?php elseif (isset($_GET['success'])): ?>
        <div class="alert alert-success">Password berhasil diperbarui. <a href="dashboard.php">Kembali ke Dashboard</a></div>
      <?php endif; ?>

      <form method="POST" action="../../controllers/AuthController.php" autocomplete="off">
        <input type="hidden" name="action" value="ganti_password">

        <div class="form-group">
          <label for="lama">Password Lama:</label>
          <input type="password" name="lama" id="lama" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="baru">Password Baru:</label>
          <input type="password" name="baru" id="baru" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="konfirmasi">Konfirmasi Password Baru:</label>
          <input type="password" name="konfirmasi" id="konfirmasi" class="form-control" required>
        </div>

        <button type="submit" class="btn-submit"><i class="fas fa-key"></i> Ganti Password</button>
      </form>
    </div>
  </div>
</div>

<?php include '../templates/footer.php'; ?>
