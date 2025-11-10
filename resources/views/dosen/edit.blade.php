@extends('layouts.app')

@section('title', 'Edit Data Dosen')

@section('content')
  <div class="profile-container">
    <h6 class="fw-bold mb-5" style="text-decoration: underline; text-underline-offset: 4px;">Edit Data Dosen</h6>

    <form id="editDosenForm" method="POST" action="{{ route('dosen.update', $dosen->kd) }}">
      @csrf @method('PUT') <div class="mb-3 row">
        <label for="kd" class="col-sm-2 col-form-label">KD</label>
        <div class="col-sm-10">
          <input type="text" id="kd" name="kd" class="form-control @error('kd') is-invalid @enderror" value="{{ old('kd', $dosen->kd) }}" autocomplete="off" required>
          @error('kd')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="mb-3 row">
        <label for="namaDosen" class="col-sm-2 col-form-label">Nama Dosen</label>
        <div class="col-sm-10">
          <input type="text" id="namaDosen" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $dosen->nama) }}" autocomplete="off" required>
          @error('nama')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="mb-3 row">
        <label for="nip" class="col-sm-2 col-form-label">NIP</label>
        <div class="col-sm-10">
          <input type="text" id="nip" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $dosen->nip) }}" autocomplete="off" required>
          @error('nip')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="mb-3 row">
        <label for="noTelp" class="col-sm-2 col-form-label">No Telp</label>
        <div class="col-sm-10">
          <input type="text" id="noTelp" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp', $dosen->no_telp) }}" autocomplete="off">
          @error('no_telp')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="mb-5 row">
        <label for="email" class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-10">
          <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $dosen->email) }}" autocomplete="off" required>
          @error('email')
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
  // Tombol Batal kembali ke halaman sebelumnya
  document.getElementById('btnBatal').addEventListener('click', () => {
    window.history.back();
  });

  // HAPUS SCRIPT LAMA `customAlert` di sini.
  // Biarkan form submit secara normal.
</script>
@endpush