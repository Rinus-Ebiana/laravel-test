@extends('layouts.app')

@section('title', 'Edit Jadwal Perkuliahan')

@section('content')
  <div class="container mt-5 mb-4">
    <button class="btn-back d-flex align-items-center gap-1 px-0" onclick="window.history.back()">
      <i class="bi bi-chevron-left" style="font-size: 1.2rem;"></i>
      <span>Kembali</span>
    </button>
  </div>

  <div class="container d-flex justify-content-center align-items-center mt-3" style="max-width:1300px;">
    {{-- Nama jadwal diambil dari $schedule --}}
    <h3 id="namaJadwalTampil" class="text-black fw-semibold">{{ $schedule->nama_jadwal }}</h3>
  </div>

  <div class="container mt-3" style="max-width:1300px;">
    <div class="table-responsive" style="max-width:1300px;">
      <table class="table align-middle text-center custom-table table-jadwal">
        <thead class="align-middle">
          <tr>
            <th rowspan="2">Matakuliah</th>
            <th colspan="1">Senin</th>
            <th colspan="1">Selasa</th>
            <th colspan="1">Rabu</th>
            <th colspan="1">Kamis</th>
            <th colspan="1">Jumat</th>
            <th colspan="3">Sabtu</th>
          </tr>
          <tr>
            <th>18:00–20:30</th>
            <th>18:00–20:30</th>
            <th>18:00–20:30</th>
            <th>18:00–20:30</th>
            <th>18:00–20:30</th>
            <th>13:00–15:30</th>
            <th>15:30–18:00</th>
            <th>18:00–20:30</th>
          </tr>
        </thead>
        <tbody class="bg-white">
          @php $currentSemester = 0; @endphp
          @foreach($matakuliah as $mk)
            
            @if ($mk->semester != $currentSemester)
              @php $currentSemester = $mk->semester; @endphp
              <tr class="table-group-divider">
                <td colspan="9" class="text-start fw-bold p-2" style="background-color: #f0f0f0;">SEMESTER {{ $mk->semester }}</td>
              </tr>
            @endif
            
            <tr data-kode-mk="{{ $mk->kode_mk }}" data-semester="{{ $mk->semester }}">
              <td class="text-start">{{ $mk->nama_mk }}</td>
              
              @php
                $dosenList = $mk->dosen->map(function($d) {
                    return ['kd' => $d->kd, 'nama' => $d->nama];
                });
              @endphp

              <td data-dosen="{{ json_encode($dosenList) }}" data-hari="Senin" data-jam="18:00–20:30"></td>
              <td data-dosen="{{ json_encode($dosenList) }}" data-hari="Selasa" data-jam="18:00–20:30"></td>
              <td data-dosen="{{ json_encode($dosenList) }}" data-hari="Rabu" data-jam="18:00–20:30"></td>
              <td data-dosen="{{ json_encode($dosenList) }}" data-hari="Kamis" data-jam="18:00–20:30"></td>
              <td data-dosen="{{ json_encode($dosenList) }}" data-hari="Jumat" data-jam="18:00–20:30"></td>
              <td data-dosen="{{ json_encode($dosenList) }}" data-hari="Sabtu" data-jam="13:00–15:30"></td>
              <td data-dosen="{{ json_encode($dosenList) }}" data-hari="Sabtu" data-jam="15:30–18:00"></td>
              <td data-dosen="{{ json_encode($dosenList) }}" data-hari="Sabtu" data-jam="18:00–20:30"></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div id="popupNamaJadwal" class="popup-overlay" style="display: none;"></div>

  <div class="container mt-4 mb-5 d-flex justify-content-end gap-2" style="max-width:1300px;">
    <button class="btn btn-custom px-4 me-1" id="btnSimpanPermanen">Simpan Permanen</button>
    <button class="btn btn-custom px-4" id="btnSimpanSementara">Simpan Sementara</button>
  </div>

  <div class="modal fade" id="confirmPermanentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-sm">
        <div class="modal-body text-center p-4">
          <h6 class="mb-3 fw-semibold text-dark">
            Jadwal tidak dapat diubah lagi setelah simpan permanen.
          </h6>
          <div class="d-flex justify-content-center gap-3">
            <button type="button" class="btn btn-custom px-4" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-custom px-4" id="confirmPermanentSave">Simpan</button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
    // --- [BAGIAN 1: SET NAMA JADWAL] ---
    let namaJadwal = "{{ $schedule->nama_jadwal }}";

    // --- [BAGIAN 2: LOGIKA GRID INTERAKTIF (DENGAN PERBAIKAN BUG)] ---
    function getColorByKode(kode) {
        const colors = { "GAP": "#FAA6A6", "DPH": "#9BEC92", "RRH": "#F1F59C", "DH": "#8796F6", "NPS": "#F1C173", "ET": "#86E8E6" };
        return colors[kode] || "#e5e8e8";
    }

    let bookedLecturers = {};
    let bookedSemesterSlots = {}; 

    document.querySelectorAll(".table-jadwal tbody tr[data-kode-mk]").forEach(row => {
        const matkulKode = row.dataset.kodeMk;
        const semester = row.dataset.semester;

        row.querySelectorAll("td[data-dosen]").forEach(cell => {
            const dosenList = JSON.parse(cell.dataset.dosen);
            const hari = cell.dataset.hari;
            const jam = cell.dataset.jam;
            const cellKey = `${hari} ${jam}`;
            const semesterCellKey = `Semester ${semester} - ${cellKey}`;

            if (dosenList.length === 0) {
                cell.style.backgroundColor = '#f0f0f0';
                return;
            }
            cell.style.position = "relative";
            cell.style.cursor = "pointer";

            // Event Hover (MouseEnter)
            cell.addEventListener("mouseenter", () => {
                if (cell.dataset.fixed === "true" || row.classList.contains("locked")) return;
                
                let htmlPreview = '';
                const isSemesterSlotBooked = bookedSemesterSlots[semesterCellKey];

                dosenList.forEach(dosen => {
                    const isDosenBooked = bookedLecturers[cellKey] && bookedLecturers[cellKey].includes(dosen.kd);
                    let hoverStyle = '';
                    let title = dosen.nama;

                    if (isDosenBooked) {
                        hoverStyle = 'opacity: 0.3; text-decoration: line-through;';
                        title = `${dosen.nama} (Dosen ini sudah mengajar di slot ini)`;
                    } else if (isSemesterSlotBooked) {
                        hoverStyle = 'opacity: 0.3; background: #aaa; color: #555;';
                        title = `${dosen.nama} (Slot sudah terisi di semester ini)`;
                    }

                    htmlPreview += `<div class="hover-option" data-kode="${dosen.kd}" 
                        style="flex: 1; height: 100%; background: ${getColorByKode(dosen.kd)}; display: flex; align-items: center; justify-content: center; font-weight: 600; ${hoverStyle}"
                        title="${title}">${dosen.kd}</div>`;
                });
                
                const preview = document.createElement("div");
                preview.style.cssText = `position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; z-index: 5;`;
                preview.innerHTML = htmlPreview;
                cell.appendChild(preview);
            });

            // Event Hover (MouseLeave)
            cell.addEventListener("mouseleave", () => {
                if (cell.dataset.fixed !== "true" && !row.classList.contains("locked")) {
                    cell.innerHTML = "";
                }
            });

            // Event Klik
            cell.addEventListener("click", e => {
                if (cell.dataset.fixed === "true") {
                    const chosenDosen = cell.dataset.chosenDosen;
                    
                    if (bookedLecturers[cellKey]) {
                        bookedLecturers[cellKey] = bookedLecturers[cellKey].filter(d => d !== chosenDosen);
                        if (bookedLecturers[cellKey].length === 0) {
                            delete bookedLecturers[cellKey];
                        }
                    }
                    delete bookedSemesterSlots[semesterCellKey];
                    
                    cell.innerHTML = "";
                    cell.dataset.fixed = "false";
                    cell.dataset.chosenDosen = "";
                    row.classList.remove("locked");
                    return;
                }

                if (row.classList.contains("locked")) return;
                if (!e.target.classList.contains('hover-option')) return;
                
                const chosenDosenKd = e.target.dataset.kode;

                if (bookedLecturers[cellKey] && bookedLecturers[cellKey].includes(chosenDosenKd)) {
                    alert(`Dosen ${chosenDosenKd} sudah mengajar di slot ${cellKey} (di semester lain).`);
                    return;
                }
                
                if (bookedSemesterSlots[semesterCellKey]) {
                    alert(`Slot ${cellKey} sudah digunakan untuk matakuliah lain di Semester ${semester}.`);
                    return;
                }

                if (!bookedLecturers[cellKey]) {
                    bookedLecturers[cellKey] = [];
                }
                bookedLecturers[cellKey].push(chosenDosenKd);
                bookedSemesterSlots[semesterCellKey] = true;
                
                cell.innerHTML = "";
                const div = document.createElement("div");
                div.style.cssText = `position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: ${getColorByKode(chosenDosenKd)}; display: flex; align-items: center; justify-content: center; font-weight: 600;`;
                div.textContent = chosenDosenKd;
                cell.appendChild(div);
                
                cell.dataset.fixed = "true";
                cell.dataset.chosenDosen = chosenDosenKd;
                row.classList.add("locked");
            });
        });
    });

    // --- [BAGIAN 3: LOGIKA PENGISIAN DATA (HYDRATION)] ---
    const existingEntries = @json($schedule->entries);
    
    existingEntries.forEach(entry => {
        const { matakuliah_kode_mk, dosen_kd, hari, jam_slot } = entry;
        
        const row = document.querySelector(`tr[data-kode-mk="${matakuliah_kode_mk}"]`);
        if (!row) return;

        const cell = row.querySelector(`td[data-hari="${hari}"][data-jam="${jam_slot}"]`);
        if (!cell) return;

        const semester = row.dataset.semester;
        const cellKey = `${hari} ${jam_slot}`;
        const semesterCellKey = `Semester ${semester} - ${cellKey}`;

        if (!bookedLecturers[cellKey]) {
            bookedLecturers[cellKey] = [];
        }
        bookedLecturers[cellKey].push(dosen_kd);
        bookedSemesterSlots[semesterCellKey] = true; 

        const div = document.createElement("div");
        div.style.cssText = `position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: ${getColorByKode(dosen_kd)}; display: flex; align-items: center; justify-content: center; font-weight: 600;`;
        div.textContent = dosen_kd;
        cell.appendChild(div);

        cell.dataset.fixed = "true";
        cell.dataset.chosenDosen = dosen_kd;
        row.classList.add("locked");
    });


    // --- [BAGIAN 4: LOGIKA SIMPAN (AJAX) - DIPERBARUI] ---

    // Inisialisasi HANYA modal yang ada di halaman ini
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmPermanentModal'));

    // **BARIS YANG BERKONFLIK SUDAH DIHAPUS DARI SINI**
    // const successModal = new bootstrap.Modal(document.getElementById('customAlert')); // <-- DIHAPUS
    // const successModalLabel = document.getElementById('customAlertLabel'); // <-- DIHAPUS

    async function saveSchedule(isPermanent) {
        let entries = [];
        document.querySelectorAll('td[data-fixed="true"]').forEach(cell => {
            const row = cell.closest('tr');
            entries.push({
                mk: row.dataset.kodeMk,
                dosen: cell.dataset.chosenDosen,
                hari: cell.dataset.hari,
                jam: cell.dataset.jam
            });
        });

        if (entries.length === 0) {
            alert("Jadwal masih kosong. Silakan pilih setidaknya satu matakuliah.");
            return;
        }
        
        try {
            const response = await fetch("{{ route('jadwal.update', $schedule->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    _method: 'PUT',
                    nama_jadwal: namaJadwal, 
                    is_permanent: isPermanent,
                    entries: entries
                })
            });

            const result = await response.json();

            if (result.success) {
                // **PERBAIKAN: PANGGIL FUNGSI GLOBAL 'customAlert' DARI MODAL.JS**
                if (typeof customAlert === 'function') {
                    customAlert(result.message, () => {
                        window.location.href = "{{ route('jadwal.index') }}";
                    });
                } else {
                    alert(result.message);
                    window.location.href = "{{ route('jadwal.index') }}";
                }
            } else {
                alert("Gagal menyimpan: " + (result.message || "Error tidak diketahui"));
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Terjadi error saat menghubungi server.");
        }
    }
    
    // **PERBAIKAN TYPO DI SINI**
    document.getElementById('btnSimpanSementara').addEventListener('click', () => saveSchedule(false));
    document.getElementById('btnSimpanPermanen').addEventListener('click', () => { // <-- 'Permanen' BUKAN 'PermanEN'
        confirmModal.show();
    });
    document.getElementById('confirmPermanentSave').addEventListener('click', () => {
        confirmModal.hide();
        saveSchedule(true);
    });
</script>
@endpush