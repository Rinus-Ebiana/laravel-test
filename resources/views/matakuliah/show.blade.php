@extends('layouts.app')

@section('title', 'Detail Matakuliah')

@section('content')
<div class="container mt-5 mb-3">
  <button class="btn-back d-flex align-items-center gap-1 px-0" onclick="window.location.href='{{ route('matakuliah.index') }}'">
    <i class="bi bi-chevron-left" style="font-size: 1.2rem;"></i>
    <span>Kembali</span>
  </button>
</div>

  <div class="container mt-3 mb-5">
    <div class="table-responsive">
      <table class="table align-middle text-center custom-table table-matakuliah">
        <thead>
          <tr>
            <th>Kode MK</th>
            <th>Matakuliah</th>
            <th>SKS</th>
            <th>Semester</th>
            <th>Dosen Pengampu</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white">
          <tr>
            <td>{{ $matakuliah->kode_mk }}</td>
            <td>{{ $matakuliah->nama_mk }}</td>
            <td>{{ $matakuliah->sks }}</td>
            <td>{{ $matakuliah->semester }}</td>
            <td class="text-start">
              @forelse($matakuliah->dosen as $d)
                {{ $d->nama }}<br>
              @empty
                -
              @endforelse
              </td>
            <td>
              <div class="d-flex justify-content-center gap-2 icon-action">
                <a href="{{ route('matakuliah.edit', $matakuliah->kode_mk) }}" class="icon-btn edit"><i class="bi bi-pencil-square fs-5"></i></a>
                <a href="#" class="icon-btn delete" data-bs-toggle="modal" data-bs-target="#deleteModal"><i
                    class="bi bi-trash fs-5"></i></a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <form id="delete-form" method="POST" action="{{ route('matakuliah.destroy', $matakuliah->kode_mk) }}" style="display: none;">
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