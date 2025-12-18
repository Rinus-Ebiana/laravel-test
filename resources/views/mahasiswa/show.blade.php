@extends('layouts.app')

@section('title', 'Detail Mahasiswa')

@section('content')
  <div class="container mt-5 mb-4">
    <button class="btn-back d-flex align-items-center gap-1 px-0" onclick="window.location.href='{{ route('mahasiswa.index') }}'">
      <i class="bi bi-chevron-left" style="font-size: 1.2rem;"></i>
      <span>Kembali</span>
    </button>
  </div>

  <div class="profile-container">
    <h6 class="fw-bold mb-5" style="text-decoration: underline; text-underline-offset: 4px;">Detail Mahasiswa</h6>

    <div class="mb-4 d-flex align-items-start">
      <label class="fw-bold text-start" style="min-width: 130px;">NIM</label>
      <span class="me-2">:</span>
      <span class="text-start">{{ $mahasiswa->nim }}</span>
    </div>

    <div class="mb-4 d-flex align-items-start">
      <label class="fw-bold text-start" style="min-width: 130px;">Nama</label>
      <span class="me-2">:</span>
      <span class="text-start">{{ $mahasiswa->nama }}</span>
    </div>

    <div class="mb-4 d-flex align-items-start">
      <label class="fw-bold text-start" style="min-width: 130px;">Semester</label>
      <span class="me-2">:</span>
      <span class="text-start">{{ $mahasiswa->semester }}</span>
    </div>

    <div class="mb-4 d-flex align-items-start">
      <label class="fw-bold text-start" style="min-width: 130px;">Tahun Masuk</label>
      <span class="me-2">:</span>
      <span class="text-start">{{ $mahasiswa->tahun_masuk_string }}</span>
    </div>

    <div class="mb-4 d-flex align-items-start">
      <label class="fw-bold text-start" style="min-width: 130px;">No Telp</label>
      <span class="me-2">:</span>
      <span class="text-start">{{ $mahasiswa->no_telp }}</span>
    </div>

    <div class="mb-4 d-flex align-items-start">
      <label class="fw-bold text-start" style="min-width: 130px;">Email</label>
      <span class="me-2">:</span>
      <span class="text-start">{{ $mahasiswa->email }}</span>
    </div>

    <div class="btn-wrapper">
      {{-- <button type="button" class="btn btn-custom px-4 me-1" id="btnBatal" onclick="window.history.back()">Batal</button> --}}
      <a href="{{ route('mahasiswa.edit', $mahasiswa->nim) }}" class="btn btn-custom px-4">Edit</a>
      <button class="btn btn-custom px-4" data-bs-toggle="modal" data-bs-target="#deleteModal">Hapus</button>
    </div>
  </div>

  <form id="delete-form" method="POST" action="{{ route('mahasiswa.destroy', $mahasiswa->nim) }}" style="display: none;">
    @csrf
    @method('DELETE')
  </form>
  @endsection

@push('scripts')
<script>
// Skrip ini menghubungkan modal konfirmasi dengan form DELETE
document.addEventListener("DOMContentLoaded", () => {
  const confirmDeleteButton = document.getElementById('confirmDelete');
  const deleteForm = document.getElementById('delete-form');

  if(confirmDeleteButton && deleteForm) {
    confirmDeleteButton.addEventListener('click', function() {
      deleteForm.submit();
    });
  }
});
</script>
@endpush