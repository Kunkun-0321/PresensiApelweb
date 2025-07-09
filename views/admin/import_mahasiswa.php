<?php include '../templates/header.php'; ?>
<?php include '../templates/navbar.php'; ?>

<h3><i class="fas fa-file-import"></i> Import Mahasiswa via CSV</h3>
<p>Gunakan file CSV dengan format kolom: <code>NIM, Nama, Kelas, Tingkat</code></p>

<form action="../../controllers/MahasiswaController.php" method="POST" enctype="multipart/form-data">
  <input type="hidden" name="action" value="import_csv">

  <label for="csv_file">Pilih File CSV:</label>
  <input type="file" name="csv_file" accept=".csv" required>
  <br><br>
  <button type="submit"><i class="fas fa-upload"></i> Upload</button>
</form>

<?php include '../templates/footer.php'; ?>
