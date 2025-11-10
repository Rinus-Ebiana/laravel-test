@extends('layouts.app')

@section('title', 'Detail Mahasiswa')

@section('content')
  <div class="container mt-5 mb-4">
    <button class="btn-back d-flex align-items-center gap-1 px-0" onclick="window.location.href='{{ route('mahasiswa.index') }}'">
      <i class="bi bi-chevron-left" style="font-size: 1.2rem;"></i>
      <span>Kembali</span>
    </button>
  </div>

  <div class="container mt-3">
    <div class="table-responsive">
      <table class="table align-middle text-center custom-table table-mahasiswa">
        <thead>
          <tr>
            <th>Semester</th>
            <th>NIM</th>
            <th>Nama</th>
            <th>No Telp</th>
            <th>Email</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white">
          <tr>
            <td>{{ $mahasiswa->semester }}</td> <td>{{ $mahasiswa->nim }}</td>
            <td>{{ $mahasiswa->nama }}</td>
            <td>{{ $mahasiswa->no_telp }}</td>
            <td>{{ $mahasiswa->email }}</td>
            <td>
              <div class="d-flex justify-content-center gap-2 icon-action">
                <a href="{{ route('mahasiswa.edit', $mahasiswa->nim) }}" class="icon-btn edit"><i class="bi bi-pencil-square fs-5"></i></a>
                <a href="#" class="icon-btn delete" data-bs-toggle="modal" data-bs-target="#deleteModal"><i
                    class="bi bi-trash fs-5"></i></a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
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