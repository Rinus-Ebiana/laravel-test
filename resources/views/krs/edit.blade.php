@extends('layouts.app')

@section('title', 'Edit Data KRS')

@section('content')
  <div class="profile-container">
    <h6 class="fw-bold mb-5" style="text-decoration: underline; text-underline-offset: 4px;">Edit Data KRS</h6>

    <form id="editKrsForm" action="{{ route('krs.update', ['id' => $krs->id ?? 1]) }}" method="POST">
      @csrf
      @method('PUT')
      
      <div class="mb-3 row">
        <label for="krs" class="col-sm-2 col-form-label">Nama KRS</label>
        <div class="col-sm-10">
          <input type="text" id="krs" name="nama_krs" class="form-control" value="{{ $krs->nama ?? 'Placeholder' }}" autocomplete="off" required>
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

  // Skrip alert dari file asli
  document.getElementById('editKrsForm').addEventListener('submit', (e) => {
    e.preventDefault();
    customAlert('Data berhasil disimpan.', () => {
      e.target.reset();
      window.history.back();
    });
  });
</script>
@endpush