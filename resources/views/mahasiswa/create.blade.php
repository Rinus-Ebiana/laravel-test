@extends('layouts.app')

@section('title', 'Tambah Data Mahasiswa')

@section('content')
  <div class="profile-container">
    <h6 class="fw-bold mb-5" style="text-decoration: underline; text-underline-offset: 4px;">Tambah Data Mahasiswa</h6>

    <form id="tambahMahasiswaForm" method="POST" action="{{ route('mahasiswa.store') }}">
      @csrf 

      <div class="mb-3 row">
        <label for="nim" class="col-sm-2 col-form-label">NIM</label>
        <div class="col-sm-10">
          <input type="text" id="nim" name="nim" class="form-control @error('nim') is-invalid @enderror" value="{{ old('nim') }}" autocomplete="off" required>
          @error('nim')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="mb-3 row">
        <label for="nama" class="col-sm-2 col-form-label">Nama</label>
        <div class="col-sm-10">
          <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" autocomplete="off" required>
          @error('nama')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="mb-3 row">
        <label for="tahun_masuk_string" class="col-sm-2 col-form-label">Tahun Masuk</label>
        <div class="col-sm-10">
          <input type="text" id="tahun_masuk_string" name="tahun_masuk_string" 
                 class="form-control @error('tahun_masuk_string') is-invalid @enderror" 
                 value="{{ old('tahun_masuk_string') }}" 
                 placeholder="Contoh: T.A. GANJIL 2023/2024" 
                 autocomplete="off" required>
          @error('tahun_masuk_string')
            <div class="invalid-feedback">{{ $message }}</div>
          @endError
        </div>
      </div>

      <div class="mb-3 row">
        <label for="noTelp" class="col-sm-2 col-form-label">No Telp</label>
        <div class="col-sm-10">
          <input type="text" id="noTelp" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp') }}" autocomplete="off">
          @error('no_telp')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      
      <div class="btn-wrapper">
        <button type="button" class="btn btn-custom px-4 me-1" id="btnBatal">Batal</button>
        <button type="submit" class="btn btn-custom px-4">Simpan</button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
<script>
  document.getElementById('btnBatal').addEventListener('click', () => {
    window.history.back();
  });
  // Kita hapus script customAlert agar form submit normal
</script>
@endpush