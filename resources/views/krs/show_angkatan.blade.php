@extends('layouts.app')
@section('title', 'Detail KRS Angkatan')

@section('content')


  <div class="container mt-5">
    <button class="btn-back d-flex align-items-center gap-1 px-0" onclick="window.location.href='{{ route('krs.index') }}'">
      <i class="bi bi-chevron-left" style="font-size: 1.2rem;"></i>
      <span>Kembali</span>
    </button>
  </div>

  <h4 class="fw-semibold mb-3 text-center">Angkatan {{ $angkatan }}</h4>

<div class="container search-section">
  <form action="{{ route('krs.showAngkatan', $slug) }}" method="GET" class="search-bar" id="searchForm" style="position: relative;"> 
    <input type="text" 
           name="search" 
           id="searchInput" 
           class="form-control" 
           placeholder="Cari NIM atau Nama Mahasiswa..."
           value="{{ $search ?? '' }}"
           style="padding-left: 35px;"> 
    <i class="bi bi-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); z-index: 10;"></i>
  </form>
  
  <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#modalDownloadKrs">
    Unduh
  </button>
  <a href="{{ route('krs.susunAngkatan', ['slug' => $slug]) }}" class="btn btn-custom" id="btnTambah">Susun KRS</a>
</div>

  <div class="container mt-3 mb-5">
    <div class="table-responsive">
      <table class="table align-middle text-center custom-table table-mahasiswa">
        <thead class="align-middle">
          <tr>
            <th>No</th> 
            <th>Tahun Masuk</th>
            <th>NIM</th> 
            <th>Nama</th> 
            <th>Semester</th> 
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white" id="tableBody"> 
          @include('krs._show_angkatan_table_rows')
        </tbody>
      </table>
    </div>
  </div>

  <div class="modal fade" id="modalDownloadKrs" tabindex="-1" aria-labelledby="modalDownloadKrsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalDownloadKrsLabel">
                    Unduh KRS Angkatan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <p>Silakan pilih format file:</p>

                <button
                    type="button"
                    class="btn btn-primary me-2"
                    onclick="downloadFile('{{ route('krs.downloadAngkatan', ['slug' => $slug]) }}')">
                    Unduh PDF
                </button>

                <button
                    type="button"
                    class="btn btn-primary"
                    onclick="downloadFile('{{ route('krs.downloadAngkatanExcel', ['slug' => $slug]) }}')">
                    Unduh Excel
                </button>
            </div>

        </div>
    </div>
  </div>

<script>
function downloadFile(url) {
    // Tutup modal
    const modalEl = document.getElementById('modalDownloadKrs');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();

    // Mulai download
    window.location.href = url;
}
</script>

@endsection