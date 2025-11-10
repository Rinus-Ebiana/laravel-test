@extends('layouts.app')

@section('title', 'History Jadwal')

@section('content')
  <div class="container mt-5 mb-4">
    <button class="btn-back d-flex align-items-center gap-1 px-0" onclick="window.location.href='{{ route('jadwal.index') }}'">
      <i class="bi bi-chevron-left" style="font-size: 1.2rem;"></i>
      <span>Kembali</span>
    </button>
  </div>

  <div id="title">History Jadwal Permanen</div>

  <div class="container mt-3" style="max-width:1300px;">
    @forelse($schedules as $schedule)
      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">{{ $schedule->nama_jadwal }}</h5>
          <small>Disimpan pada: {{ $schedule->created_at->format('d M Y H:i') }}</small>
        </div>
        <div class="card-body">
          <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
            <table class="table align-middle text-center custom-table table-jadwal table-sm">
              <thead class="align-middle">
                <tr>
                  <th>Matakuliah</th>
                  <th>Dosen</th>
                  <th>Hari</th>
                  <th>Jam</th>
                </tr>
              </thead>
              <tbody class="bg-white">
                @foreach($schedule->entries as $entry)
                <tr>
                  <td class="text-start">{{ $entry->matakuliah->nama_mk }}</td>
                  <td class="text-start">{{ $entry->dosen->nama }}</td>
                  <td>{{ $entry->hari }}</td>
                  <td>{{ $entry->jam_slot }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <button class="btn btn-sm btn-danger icon-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-url="{{ route('jadwal.destroy', $schedule->id) }}">
                <i class="bi bi-trash fs-5"></i>
            </button>
            <a href="{{ route('jadwal.downloadPerDosen', $schedule->id) }}" class="btn btn-sm btn-custom px-3">Unduh Per Dosen</a>
            <a href="{{ route('jadwal.downloadSemua', $schedule->id) }}" class="btn btn-sm btn-custom px-3">Unduh Semua</a>
        </div>
      </div>
    @empty
      <div class="text-center" style="margin-top: 5rem;">
        <h5>Belum ada jadwal yang disimpan secara permanen.</h5>
      </div>
    @endforelse
  </div>

  <form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
  </form>
@endsection

@push('scripts')
<script>
// Skrip untuk modal delete dinamis
document.addEventListener("DOMContentLoaded", () => {
  const deleteModal = document.getElementById('deleteModal');
  if (deleteModal) {
    const confirmDeleteButton = document.getElementById('confirmDelete');
    const deleteForm = document.getElementById('delete-form');
    
    deleteModal.addEventListener('show.bs.modal', function (event) {
      // Tombol yang memicu modal
      const button = event.relatedTarget;
      // Ambil URL dari atribut data-url
      const url = button.getAttribute('data-url');
      // Set action form
      deleteForm.setAttribute('action', url);
    });

    confirmDeleteButton.addEventListener('click', function() {
      deleteForm.submit();
    });
  }
});
</script>
@endpush