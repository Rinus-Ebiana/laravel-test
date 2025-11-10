@extends('layouts.app')

@section('title', 'Import Data Mahasiswa')

@section('content')
  <div class="profile-container">
    <h6 class="fw-bold mb-5" style="text-decoration: underline; text-underline-offset: 4px;">Import Data Mahasiswa via Excel</h6>

    <form method="POST" action="{{ route('mahasiswa.storeImport') }}" enctype="multipart/form-data">
      @csrf <div class="mb-3 row">
        <label for="file" class="col-sm-2 col-form-label">File Excel</label>
        <div class="col-sm-10">
          <input type="file" id="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
          
          @error('file')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          
          <div class="form-text mt-2">
            * Gunakan file .xlsx atau .xls.<br>
            * Pastikan header file adalah: `nim`, `nama`, `tahun_masuk`, `no_telp`.
          </div>
        </div>
      </div>

      <div class="btn-wrapper">
        <button type="button" class="btn btn-custom px-4 me-1" id="btnBatal">Batal</button>
        <button type="submit" class="btn btn-custom px-4">Upload dan Import</button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
<script>
  // Tombol Batal kembali ke halaman sebelumnya
  document.getElementById('btnBatal').addEventListener('click', () => {
    window.history.back();
  });
</script>
@endpush