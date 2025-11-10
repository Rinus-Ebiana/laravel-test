<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Homepage</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    /* ... (CSS Anda dari file asli) ... */
    .overlay-biru {
      background-color: #101F6A !important;
      opacity: 0.55 !important;
    }
    .btn-glass {
      background-color: rgba(255, 255, 255, 0.15) !important;
      border: 1px solid rgba(255, 255, 255, 0.35) !important;
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      color: white !important;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .btn-glass:hover {
      background-color: rgba(255, 255, 255, 0.3) !important;
      transform: scale(1.03);
      color: white !important;
    }
    .logo-stikom {
      margin-top: -15px;
    }
  </style>
</head>
<body class="bg-dark text-white min-vh-100 d-flex flex-column justify-content-center align-items-center text-center">

  <div class="position-fixed top-0 start-0 w-100 h-100">
    <img src="{{ asset('assets/gedung.jpg') }}" alt="Background" class="w-100 h-100 object-fit-cover opacity-75">
  </div>

  <div class="position-fixed top-0 start-0 w-100 h-100 overlay-biru"></div>

  <div class="position-relative">

    <img src="{{ asset('assets/logoputih.png') }}" alt="Logo" class="img-fluid mb-4 logo-stikom" style="max-width: 125px;">

    <h3 class="fw-bold">SISTEM PERWALIAN PASCASARJANA</h3>
    <h5 class="mb-5">ITB STIKOM BALI</h5>

    <div class="container">
      <div class="row g-3 justify-content-center">

        <div class="col-10 col-sm-6 col-md-4">
          <a href="{{ route('dosen.index') }}" class="btn btn-glass w-100 py-3">
            <i class="bi bi-person-badge fs-3 d-block mb-1"></i>
            <span class="fw-semibold">Dosen</span>
          </a>
        </div>

        <div class="col-10 col-sm-6 col-md-4">
          <a href="{{ route('mahasiswa.index') }}" class="btn btn-glass w-100 py-3">
            <i class="bi bi-people fs-3 d-block mb-1"></i>
            <span class="fw-semibold">Mahasiswa</span>
          </a>
        </div>

        <div class="col-10 col-sm-6 col-md-4">
          <a href="{{ route('matakuliah.index') }}" class="btn btn-glass w-100 py-3">
            <i class="bi bi-journal-bookmark fs-3 d-block mb-1"></i>
            <span class="fw-semibold">Matakuliah</span>
          </a>
        </div>

        <div class="col-10 col-sm-6 col-md-4">
          <a href="{{ route('jadwal.index') }}" class="btn btn-glass w-100 py-3">
            <i class="bi bi-calendar-event fs-3 d-block mb-1"></i>
            <span class="fw-semibold">Jadwal</span>
          </a>
        </div>

        <div class="col-10 col-sm-6 col-md-4">
          <a href="{{ route('kelas.index') }}" class="btn btn-glass w-100 py-3">
            <i class="bi bi-building fs-3 d-block mb-1"></i>
            <span class="fw-semibold">Kelas</span>
          </a>
        </div>

        <div class="col-10 col-sm-6 col-md-4">
          <a href="{{ route('krs.index') }}" class="btn btn-glass w-100 py-3">
            <i class="bi bi-file-earmark-text fs-3 d-block mb-1"></i>
            <span class="fw-semibold">KRS</span>
          </a>
        </div>

      </div>
    </div>
  </div>
</body>
</html>