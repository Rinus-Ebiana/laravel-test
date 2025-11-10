<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit KRS</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <!-- Custom Styles -->
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/button-style.css">
  <link rel="stylesheet" href="css/table-style.css">
  <link rel="stylesheet" href="css/icon-style.css">
  <link rel="stylesheet" href="css/sort-style.css">
  <link rel="stylesheet" href="css/search-style.css">
  <link rel="stylesheet" href="css/title-style.css">
  <link rel="stylesheet" href="css/layout-style.css">

  <style>
    /* Gaya blok tombol T.A. */
    .ta-button {
      display: block;
      width: 100%;
      text-align: left;
      padding: 12px 18px;
      border: 1px solid #000000;
      border-radius: 6px;
      background-color: #ffffff;
      font-weight: 600;
      color: #000000;
      transition: all 0.2s ease;
    }

    .ta-button:hover {
      background-color: #101F6A;
      border-color: #101F6A;
      font-weight: 600;
      color: #ffffff;
    }

    /* Lebar tombol disesuaikan dengan lebar tabel halaman lain */
    .ta-container {
      max-width: 1300px;
      margin: 0 auto;
    }

    .ta-wrapper {
      margin-bottom: 16px;
    }
    
  .ta-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* Membagi jadi 2 kolom */
    gap: 0px 20px; /* Jarak antar tombol */
    max-width: 1300px;
    margin: 0 auto; /* Agar tetap di tengah halaman */
  }

  .ta-wrapper {
    width: 100%;
  }

  .ta-button {
    width: 100%;
  }
</style>


</head>

<body>
  <!-- Navbar -->
  <div id="navbar"></div>

  <!-- Menu Navigasi -->
  <div id="navigasi"></div>

  <!-- Judul Halaman -->
  <div id="title">Kartu Rencana Studi (KRS)</div>

  <br>
  <br>

  <!-- Tombol Back  -->
  <div class="container">
    <button class="btn-back d-flex align-items-center gap-1 px-0" onclick="window.history.back()">
      <i class="bi bi-chevron-left" style="font-size: 1.2rem;"></i>
      <span>Kembali</span>
    </button>
  </div>

      <!-- Tombol Aksi -->
  <div class="container mb-4 d-flex justify-content-end gap-2" style="max-width:1300px;">
    <button class="btn btn-custom px-4" onclick="window.location.href='form_tambah_krs.html'">Tambah</button>
  </div>

  <!-- Daftar Tahun Akademik -->
<div class="container ta-container mb-5">
    <div class="ta-wrapper">
      <button class="ta-button d-flex justify-content-between align-items-center"
        onclick="window.location.href='detail_krs.html'">
        <span>Placeholder</span>
        <span class="d-flex gap-2 me-2">
          <a href="form_edit_krs.html" class="icon-btn edit"><i class="bi bi-pencil-square fs-5"></i></a>
          <a href="#" class="icon-btn delete" data-bs-toggle="modal" data-bs-target="#deleteModal"><i
              class="bi bi-trash fs-5"></i></a>
        </span>
      </button>
    </div> 
</div>

<!-- Container tempat modal akan dimuat -->
<div id="modalContainer"></div>

<!-- File JS utama -->
<script src="main.js"></script>

<!-- File JS modal (berisi customAlert & initDeleteModal) -->
<script src="modal.js"></script>

<script>
// Muat file modal.html
fetch('modal.html')
  .then(response => response.text())
  .then(html => {
    document.getElementById('modalContainer').innerHTML = html;

    // Jalankan hanya fungsi delete modal
    if (typeof initDeleteModal === 'function') {
      initDeleteModal();
    }
  })
  .catch(error => console.error('Gagal memuat modal:', error));
</script>


<!-- Agar tombol delete tidak bercampur dengan tombol utama-->

<script>
document.addEventListener("DOMContentLoaded", () => {
  // Tangkap semua ikon edit dan delete
  const actionIcons = document.querySelectorAll(".icon-btn");

  actionIcons.forEach(icon => {
    icon.addEventListener("click", (e) => {
      e.stopPropagation(); // hentikan klik agar tidak trigger onclick button
    });
  });
});
</script>

  <!-- Bootstrap & Script -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/main.js"></script>
</body>

</html>