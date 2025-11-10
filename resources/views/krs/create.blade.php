<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Form Tambah KRS</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <!-- Custom Styles -->
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/button-style.css">
  <link rel="stylesheet" href="css/table-style.css">
  <link rel="stylesheet" href="css/form-style.css">
</head>

<body>
  <!-- Navbar -->
  <div id="navbar"></div>

  <!-- Navigasi -->
  <div id="navigasi"></div>

  <!-- ====== Formulir Tambah Dosen ====== -->
  <div class="profile-container">
    <h6 class="fw-bold mb-5" style="text-decoration: underline; text-underline-offset: 4px;">Tambah Data KRS</h6>

    <form id="tambahDosenForm">
      <!-- Input KRS -->
      <div class="mb-3 row">
        <label for="krs" class="col-sm-2 col-form-label">Nama KRS</label>
        <div class="col-sm-10">
          <input type="text" id="krs" class="form-control" autocomplete="off" required>
        </div>
      </div>

      <!-- Tombol Batal & Simpan -->
      <div class="btn-wrapper">
        <button type="button" class="btn btn-custom px-4 me-1" id="btnBatal">Batal</button>
        <button type="submit" class="btn btn-custom px-4">Simpan</button>
      </div>
    </form>
  </div>



<!-- Load file modal HTML -->
<div id="modalContainer"></div>

<!-- File JS utama -->
<script src="main.js"></script>

<!-- File JS untuk modal -->
<script src="modal.js"></script>
  


<!-- Script -->
  <script>
    // Tombol Batal kembali ke halaman sebelumnya
    document.getElementById('btnBatal').addEventListener('click', () => {
      window.history.back();
    });
  </script>


<script>
// Memuat isi modal.html secara dinamis ke halaman
fetch('modal.html')
  .then(response => response.text())
  .then(html => document.getElementById('modalContainer').innerHTML = html);

  document.getElementById('tambahDosenForm').addEventListener('submit', (e) => {
  e.preventDefault();
  customAlert('Data berhasil disimpan.', () => {
    e.target.reset();
    window.history.back();
  });
});
</script>


  <!-- Bootstrap & Script -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/main.js"></script>
</body>

</html>