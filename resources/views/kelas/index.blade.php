@extends('layouts.app')

@section('title', 'Kelas')

@push('styles')
<style>
  /* Efek minimalis saat sel editable aktif (dari file asli Anda) */
  td[contenteditable="true"] {
    cursor: text;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
  }
  td[contenteditable="true"]:focus {
    outline: none;
    background-color: #f5faff;
    box-shadow: inset 0 0 0 1px #cfe2ff;
    border-radius: 4px;
  }
</style>
@endpush

@section('content')
  @if($schedule)
    <div id="title">{{ $schedule->nama_jadwal }}</div>
  @else
    <div id="title">Data Kelas</div>
  @endif

  <br><br><br>

  @if($entriesBySemester->isEmpty())
    <div class="text-center" style="margin-top: 5rem;">
      <h5>Belum ada jadwal permanen yang ditemukan.</h5>
      <p>Silakan buat dan 'Simpan Permanen' sebuah jadwal di Modul Jadwal terlebih dahulu.</p>
    </div>
  @else

    @foreach($entriesBySemester as $semester => $entries)
    <div class="container mt-2 mb-5">
      <h4 class="fw-semibold mb-3 text-end">Semester {{ $semester }}</h4>
      <div class="table-responsive">
        <table class="table align-middle text-center custom-table table-kelas">
          <thead class="align-middle">
            <tr>
              <th>Kode MK</th>
              <th>Matakuliah</th>
              <th>SKS</th>
              <th>Dosen Pengampu</th>
              <th>Hari</th>
              <th>Jam</th>
              <th>Kelas</th>
              <th>Ruang</th>
            </tr>
          </thead>
          <tbody>
            @foreach($entries as $entry)
            <tr>
              <td>{{ $entry->matakuliah->kode_mk }}</td>
              <td class="text-start">{{ $entry->matakuliah->nama_mk }}</td>
              <td>{{ $entry->matakuliah->sks }}</td>
              <td class="text-start">{{ $entry->dosen->nama }} ({{ $entry->dosen->kd }})</td>
              <td>{{ $entry->hari }}</td>
              <td>{{ $entry->jam_slot }}</td>
              
              <td contenteditable="true" 
                  data-id="{{ $entry->id }}" 
                  data-field="kode_kelas">{{ $entry->kode_kelas }}</td>
              <td contenteditable="true" 
                  data-id="{{ $entry->id }}" 
                  data-field="ruang_kelas">{{ $entry->ruang_kelas }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endforeach

    <div class="container mt-4 mb-5 d-flex justify-content-end gap-2" style="max-width:1300px;">
      <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#modalDownloadKelas">
        Unduh
      </button>
      <button class="btn btn-custom px-4" id="btnSimpanKelas">Simpan</button>
    </div>
  @endif
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const btnSimpan = document.getElementById('btnSimpanKelas');
    
    if (btnSimpan) {
        btnSimpan.addEventListener('click', async () => {
            let entries = [];
            
            // 1. Kumpulkan semua data dari sel yang bisa diedit
            document.querySelectorAll('td[contenteditable="true"]').forEach(cell => {
                const id = cell.dataset.id;
                const field = cell.dataset.field;
                const value = cell.textContent.trim();
                
                // Cari apakah entri dengan ID ini sudah ada di array
                let entry = entries.find(e => e.id === id);
                if (!entry) {
                    entry = { id: id };
                    entries.push(entry);
                }
                
                // Tambahkan data (kelas atau ruang)
                if (field === 'kode_kelas') {
                    entry.kelas = value;
                } else if (field === 'ruang_kelas') {
                    entry.ruang = value;
                }
            });

            if (entries.length === 0) {
                return; // Tidak ada data untuk disimpan
            }

            // 2. Kirim data ke Controller via AJAX (Fetch)
            try {
                const response = await fetch("{{ route('kelas.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ entries: entries })
                });

                const result = await response.json();

                if (result.success) {
                    // Gunakan customAlert global dari modal.js
                    customAlert('Data kelas berhasil disimpan.', () => {
                        // Tidak perlu refresh, data sudah tersimpan
                    });
                } else {
                    alert("Gagal menyimpan: " + (result.message || "Error tidak diketahui"));
                }

            } catch (error) {
                console.error("Error:", error);
                alert("Terjadi error saat menghubungi server.");
            }
        });
    }
});
</script>

<div class="modal fade" id="modalDownloadKelas" tabindex="-1" aria-labelledby="modalDownloadKelasLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

          <div class="modal-header">
              <h5 class="modal-title" id="modalDownloadKelasLabel">
                  Unduh Jadwal Kelas
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body text-center">
              <p>Silakan pilih format file:</p>

              <button
                  type="button"
                  class="btn btn-primary me-2"
                  onclick="downloadFile('{{ route('kelas.download') }}')">
                  Unduh PDF
              </button>

              <button
                  type="button"
                  class="btn btn-primary"
                  onclick="downloadFile('{{ route('kelas.downloadExcel') }}')">
                  Unduh Excel
              </button>
          </div>

      </div>
  </div>
</div>

<script>
function downloadFile(url) {
    // Tutup modal
    const modalEl = document.getElementById('modalDownloadKelas');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();

    // Mulai download
    window.location.href = url;
}
</script>
@endpush
