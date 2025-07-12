<?php
// File: views/admin/tambah_user.php
include '../templates/header.php';
include '../templates/navbar.php';
?>

<style>
  .form-container {
    max-width: 700px;
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
  .form-container input[type="text"],
  .form-container input[type="password"],
  .form-container select {
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
    background-color: #007bff;
    color: white;
    font-weight: bold;
    border-radius: 5px;
    cursor: pointer;
  }
  .form-container button:hover {
    background-color: #0056b3;
  }
</style>

<div class="form-container">
  <h3><i class="fas fa-user-plus"></i> Tambah User</h3>
  <p>Isi data pengguna berikut secara lengkap:</p>

  <?php if (isset($_GET['success'])): ?>
      <p style="color:green;">User berhasil ditambahkan!</p>
  <?php elseif (isset($_GET['error'])): ?>
      <p style="color:red;">Terjadi kesalahan saat menyimpan data.</p>
  <?php endif; ?>

  <form method="POST" action="../../controllers/UserController.php">
    <input type="hidden" name="action" value="tambah">

    <label for="nim"><i class="fas fa-id-badge"></i> NIM:</label>
    <input type="text" name="nim">

    <label for="nama"><i class="fas fa-user"></i> Nama:</label>
    <input type="text" name="nama">

    <label for="kelas"><i class="fas fa-door-closed"></i> Kelas:</label>
    <input type="text" name="kelas">

    <label for="tingkat"><i class="fas fa-layer-group"></i> Tingkat:</label>
    <select name="tingkat">
      <option value="">-</option>
      <option value="1">1</option>
      <option value="2">2</option>
      <option value="3">3</option>
      <option value="4">4</option>
    </select>

    <label for="username"><i class="fas fa-user-tag"></i> Username:</label>
    <input type="text" name="username" required>

    <label for="password"><i class="fas fa-key"></i> Password:</label>
    <input type="password" name="password" required>

    <label for="role"><i class="fas fa-user-cog"></i> Role:</label>
    <select name="role" required>
      <option value="admin">Admin</option>
      <option value="petugas">Petugas</option>
      <option value="mahasiswa">Mahasiswa</option>
    </select>

    <button type="submit"><i class="fas fa-save"></i> Simpan</button>
    <a href="data_mahasiswa.php" style="display:inline-block; margin-top:10px; text-decoration:none;">
  <button type="button" style="background-color:#6c757d;" class="btn">
    <i class="fas fa-arrow-left"></i> Kembali ke Data Mahasiswa
  </button>
</a>

  </form>
</div>

<?php include '../templates/footer.php'; ?>
