<?php
// File: views/admin/import_user.php
include '../templates/header.php';
include '../templates/navbar.php';
?>

<style>
  .form-container {
    max-width: 600px;
    margin: 40px auto;
    border: 1px solid #ccc;
    padding: 20px;
    border-radius: 10px;
    background-color: #fdfdfd;
  }
  .form-container h3 {
    margin-bottom: 10px;
  }
  .form-container label {
    font-weight: bold;
  }
  .form-container input[type="file"] {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
  }
  .form-container button {
    padding: 10px 15px;
    border: none;
    background-color: #28a745;
    color: white;
    font-weight: bold;
    border-radius: 5px;
    cursor: pointer;
  }
  .form-container button:hover {
    background-color: #1e7e34;
  }
  .back-link {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    color: #007bff;
  }
  .back-link:hover {
    text-decoration: underline;
  }
</style>

<div class="form-container">
  <h3><i class="fas fa-file-import"></i> Import User dari CSV</h3>
  <p>Pastikan file CSV berformat: nim, nama, kelas, tingkat, username, password, role</p>

  <?php if (isset($_GET['success'])): ?>
      <p style="color:green;">Data berhasil diimpor!</p>
  <?php elseif (isset($_GET['error'])): ?>
      <p style="color:red;">Gagal mengimpor data. Pastikan format sesuai.</p>
  <?php endif; ?>

  <form method="POST" action="../../controllers/UserController.php" enctype="multipart/form-data">
    <input type="hidden" name="action" value="import">

    <label for="csv_file"><i class="fas fa-upload"></i> Pilih File CSV:</label>
    <input type="file" name="csv_file" accept=".csv" required>

    <button type="submit"><i class="fas fa-file-import"></i> Import</button>
    <a href="data_mahasiswa.php" style="display:inline-block; margin-top:10px; text-decoration:none;">
    <button type="button" style="background-color:#6c757d;" class="btn">
      <i class="fas fa-arrow-left"></i> Kembali ke Data Mahasiswa
    </button>
  </form>

</div>

<?php include '../templates/footer.php'; ?>
