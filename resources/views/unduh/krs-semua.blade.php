<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Unduh KRS Semua</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <!-- Custom Styles -->
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/button-style.css">
  <link rel="stylesheet" href="css/table-style.css">
  <link rel="stylesheet" href="css/icon-style.css">

  <style>
    #JudulHalaman {
      border-bottom: 1px solid #000;
      display: inline-block;
      padding-bottom: 3px;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <div id="navbar"></div>

  <!-- Tombol Back  -->
  <div class="container mt-5 mb-4">
    <button class="btn-back d-flex align-items-center gap-1 px-0" onclick="window.history.back()">
      <i class="bi bi-chevron-left" style="font-size: 1.2rem;"></i>
      <span>Kembali</span>
    </button>
  </div>

  <div id="tableContainer">

    <div class="container text-center mt-5 mb-4">
      <h3 id="JudulHalaman" class="fw-semibold">Kartu Rencana Studi Tahun Ajaran Ganjil 2025/2026<br>Semester Ganjil
        2025/2026</h3>
    </div>

    <!-- Tabel Data -->
    <div class="container mt-3 mb-5" style="max-width:1300px;">
      <div class="table-responsive">
        <table class="table align-middle text-center custom-table table-mahasiswa">
          <thead>
            <tr>
              <th>No</th>
              <th>Semester</th>
              <th>NIM</th>
              <th>Nama</th>
              <th>No Telp</th>
              <th>Email</th>
            </tr>
          </thead>
          <tbody class="bg-white">
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td class="text-start">Placeholder</td>
              <td></td>
              <td class="text-start"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- SEMESTER 1 -->
    <div class="container mt-2 mb-5" style="max-width:1300px;">
      <div class="table-responsive">
        <table class="table align-middle text-center custom-table">
          <thead>
            <tr>
              <th>Kode MK</th>
              <th>Matakuliah</th>
              <th>SKS</th>
              <th>Dosen Pengampu</th>
              <th>Hari</th>
              <th>Jam</th>
              <th>Kelas</th>
              <th>Ruang</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td></td>
              <td class="text-start"></td>
              <td></td>
              <td class="text-start">Placeholder</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Tombol Unduh -->
  <div class="container mt-4 mb-5 d-flex justify-content-end gap-2" style="max-width:1300px;">
    <button class="btn btn-custom px-4" id="downloadPDF">Unduh</button>
  </div>

  <!-- Script JS Download -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script>
    document.getElementById('downloadPDF').addEventListener('click', () => {
      const element = document.getElementById('tableContainer');

      // Simpan gaya asli
      const originalStyle = element.getAttribute('style') || '';

      // Tambahkan gaya sementara hanya untuk proses PDF
      element.style.width = '100%';
      element.style.maxWidth = '100%';
      element.style.margin = '0 auto';
      element.style.backgroundColor = '#fff';
      element.style.fontSize = '11px';
      element.style.borderCollapse = 'collapse';
      element.style.overflow = 'visible';

      // Konfigurasi PDF
      const opt = {
        margin: [0.2, 0.3, 0.2, 0.3],
        filename: 'Jadwal_Perkuliahan.pdf',
        image: { type: 'jpeg', quality: 1 },
        html2canvas: {
          scale: 2,
          useCORS: true,
          scrollY: 0,
          windowWidth: element.scrollWidth
        },
        jsPDF: {
          unit: 'in',
          format: 'a4',
          orientation: 'landscape'
        },
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
      };

      // Proses PDF
      html2pdf().set(opt).from(element).save().then(() => {
        // Kembalikan gaya semula setelah download selesai
        element.setAttribute('style', originalStyle);
      });
    });
  </script>

  <!-- Bootstrap & Script -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/main.js"></script>
</body>

</html>