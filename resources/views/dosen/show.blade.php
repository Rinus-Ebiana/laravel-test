@extends('layouts.app')

@section('title', 'Detail Dosen')

@section('content')
  <div class="container mt-5 mb-4">
    <button class="btn-back d-flex align-items-center gap-1 px-0" onclick="window.location.href='{{ route('dosen.index') }}'">
      <i class="bi bi-chevron-left" style="font-size: 1.2rem;"></i>
      <span>Kembali</span>
    </button>
  </div>

  <div class="container mt-3 mb-5">
    <div class="table-responsive">
      <table class="table align-middle text-center custom-table table-dosen" id="tabelDosen">
        <thead>
          <tr>
            <th>KD</th>
            <th>Nama</th>
            <th>NIP</th>
            <th>No Telp</th>
            <th>Email</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white">
          <tr>
            <td>{{ $dosen->kd }}</td>
            <td>{{ $dosen->nama }}</td>
            <td>{{ $dosen->nip }}</td>
            <td>{{ $dosen->no_telp }}</td>
            <td>{{ $dosen->email }}</td>
            <td>
              <div class="d-flex justify-content-center gap-2 icon-action">
                
                <a href="{{ route('dosen.edit', ['dosen' => $dosen->kd]) }}" class="icon-btn edit"><i class="bi bi-pencil-square fs-5"></i></a>
                
                <a href="#" class="icon-btn delete" data-bs-toggle="modal" data-bs-target="#deleteModal"><i
                    class="bi bi-trash fs-5"></i></a>

              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div id="searchSection" data-target="tabelMatakuliah" data-add="none"></div>

  <div class="container mt-3 mb-5">
    <div class="table-responsive">
      <table class="table align-middle text-center custom-table table-matakuliah" id="tabelMatakuliah">
        <thead class="align-middle">
          <tr>
            <th>Kode MK</th>
            <th>Matakuliah</th>
            <th>SKS</th>
            <th>Semester</th>
          </tr>
        </thead>
        <tbody class="bg-white">
          @forelse($dosen->matakuliah as $mk)
            <tr>
              <td>{{ $mk->kode_mk }}</td>
              <td class="text-start">{{ $mk->nama_mk }}</td>
              <td>{{ $mk->sks }}</td>
              <td>{{ $mk->semester }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4"><i>Dosen ini belum mengampu matakuliah.</i></td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <form id="delete-form" method="POST" action="{{ route('dosen.destroy', ['dosen' => $dosen->kd]) }}" style="display: none;">
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