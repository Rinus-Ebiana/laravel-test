@extends('layouts.app')

@section('title', 'Data Mahasiswa')

@section('content')
  <div id="title">Data Mahasiswa Pascasarjana</div>
  
  <div class="container search-section">
    
    {{-- FORM dengan ID untuk Live Search AJAX --}}
    <form action="{{ route('mahasiswa.index') }}" method="GET" class="search-bar" id="searchForm" style="position: relative;">
      
      {{-- Input Pencarian. Placeholder diperbarui --}}
      <input type="text" 
             name="search" 
             id="searchInput" 
             class="form-control" 
             placeholder="Cari NIM, Nama, Tahun Masuk, atau Semester..."
             value="{{ $search ?? '' }}"
             style="padding-left: 35px;"> 
      
      {{-- Ikon Pencarian. --}}
      <i class="bi bi-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); z-index: 10;"></i>

    </form>
    
    <a href="{{ route('mahasiswa.importForm') }}" class="btn btn-custom" style="margin: 0; padding: 0 1.5rem; height: 38px; display: flex; align-items: center;">Import</a>

    <a href="{{ route('mahasiswa.create') }}" class="btn btn-custom" id="btnTambah">Tambah</a>
  </div>

  <div class="container mt-3 mb-5">
    <div class="table-responsive">
      <table class="table align-middle text-center custom-table table-mahasiswa" id="tabelMahasiswa">
        <thead class="align-middle">
          <tr>
            <th>No</th>
            <th>
              Tahun Masuk
              <button class="btn btn-sm btn-sort" data-column="1"></button>
            </th>
            <th>
              Semester
              <button class="btn btn-sm btn-sort" data-column="2"></button>
            </th>
            <th>
              NIM
              <button class="btn btn-sm btn-sort" data-column="3"></button>
            </th>
            <th>
              Nama
              <button class="btn btn-sm btn-sort" data-column="4"></button>
            </th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        {{-- FIX KRITIS: Tambahkan ID tableBody dan gunakan @include view parsial --}}
        <tbody class="bg-white" id="tableBody"> 
          @include('mahasiswa._table_rows')
        </tbody>
      </table>
    </div>
  </div>

  <br>
  <br>

  <!-- Delete Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Apakah Anda yakin ingin menghapus mahasiswa <strong id="deleteNama"></strong> (NIM: <strong id="deleteNim"></strong>)?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <form id="deleteForm" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Hapus</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Handle delete modal
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const nim = button.getAttribute('data-nim');
      const nama = button.getAttribute('data-nama');

      document.getElementById('deleteNim').textContent = nim;
      document.getElementById('deleteNama').textContent = nama;
      document.getElementById('deleteForm').action = `/mahasiswa/${nim}`;
    });
  </script>
@endsection
