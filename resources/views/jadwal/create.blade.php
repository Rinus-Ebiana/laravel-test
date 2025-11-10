@extends('layouts.app')

@section('title', 'Buat Jadwal Perkuliahan')

@push('styles')
<style>
  /* Pop-up overlay */
  .popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
  }

  /* Kotak pop-up */
  .popup-box {
    background: #fff;
    padding: 40px 60px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    width: 500px;
    max-width: 90%;
  }

  /* Efek border biru saat input difokuskan */
  .popup-box input.form-control {
    border: 1px solid #6c757d;
    transition: all 0.3s ease;
  }
  .popup-box input.form-control:focus {
    border-color: #0d6efd; /* warna biru Bootstrap */
    box-shadow: 0 0 5px rgba(13, 110, 253, 0.3);
  }
</style>
@endpush

@section('content')
  <div class="container mt-5 mb-4">
    <button class="btn-back d-flex align-items-center gap-1 px-0" onclick="window.history.back()">
      <i class="bi bi-chevron-left" style="font-size: 1.2rem;"></i>
      <span>Kembali</span>
    </button>
  </div>

  <div class="container d-flex justify-content-center align-items-center mt-3" style="max-width:1300px;">
    <h3 id="namaJadwalTampil" class="text-black fw-semibold">(Nama Jadwal Baru)</h3>
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
            <th>18:00–20:30</th> <th>18:00–20:30</th> <th>18:00–20:30</th> <th>18:00–20:30</th> <th>18:00–20:30</th> <th>13:00–15:30</th> <th>15:30–18:00</th> <th>18:00–20:30</th> </tr>
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

  <div id="popupNamaJadwal" class="popup-overlay" style="display: flex;">
    <div class="popup-box">
      <h5 class="text-center mb-4">Nama Jadwal Baru</h5>
      <form id="formNamaJadwal">
        <input type="text" id="inputNamaJadwal" class="form-control" placeholder="Masukkan nama jadwal" autocomplete="off" required>
        <div class="d-flex justify-content-center gap-4 mt-3">
          <button type="button" id="btnBatalPopup" class="btn btn-custom px-4">Batal</button>
          <button type="submit" id="btnSimpanPopup" class="btn btn-custom px-4">Simpan</button>
        </div>
      </form>
    </div>
  </div>

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
// --- [BAGIAN 1: LOGIKA POPUP NAMA JADWAL] ---
const popup = document.getElementById("popupNamaJadwal");
const formNamaJadwal = document.getElementById("formNamaJadwal");
const inputNamaJadwal = document.getElementById("inputNamaJadwal");
const namaTampil = document.getElementById("namaJadwalTampil");
const btnBatalPopup = document.getElementById("btnBatalPopup");

let namaJadwal = "";
namaTampil.textContent = "Memuat...";

formNamaJadwal.addEventListener("submit", (e) => {
    e.preventDefault();
    const nama = inputNamaJadwal.value.trim();
    if (nama) {
        namaJadwal = nama;
        namaTampil.textContent = namaJadwal;
        popup.style.display = "none";
    }
});
btnBatalPopup.addEventListener("click", () => window.history.back());


// --- [BAGIAN 2: LOGIKA GRID INTERAKTIF (DENGAN PERBAIKAN BUG)] ---
function getColorByKode(kode) {
    const colors = { "GAP": "#FAA6A6", "DPH": "#9BEC92", "RRH": "#F1F59C", "DH": "#8796F6", "NPS": "#F1C173", "ET": "#86E8E6" };
    return colors[kode] || "#e5e8e8";
}

let bookedLecturers = {}; // Format: { "Senin 18:00–20:30": ["GAP", "DPH"] }
let bookedSemesterSlots = {}; // Format: { "Semester 1 - Senin 18:00–20:30": true }

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


// --- [BAGIAN 3: LOGIKA SIMPAN (AJAX) - DIPERBARUI] ---

// Inisialisasi HANYA modal yang ada di halaman ini
const confirmModal = new bootstrap.Modal(document.getElementById('confirmPermanentModal'));

// **BARIS YANG BERKONFLIK SUDAH DIHAPUS DARI SINI**
// const successModal = new bootstrap.Modal(document.getElementById('customAlert')); // <-- DIHAPUS
// const successModalLabel = document.getElementById('customAlertLabel'); // <-- DIHAPUS

async function saveSchedule(isPermanent) {
    if (!namaJadwal) {
        alert("Silakan tentukan nama jadwal terlebih dahulu.");
        popup.style.display = "flex";
        inputNamaJadwal.focus();
        return;
    }

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
        const response = await fetch("{{ route('jadwal.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                nama_jadwal: namaJadwal,
                is_permanent: isPermanent,
                entries: entries
            })
        });

        const result = await response.json();

        if (result.success) {
            // **PERBAIKAN: PANGGIL FUNGSI GLOBAL 'customAlert' DARI MODAL.JS**
            // Pastikan Anda memiliki fungsi 'customAlert' di 'modal.js'
            if (typeof customAlert === 'function') {
                customAlert(result.message, () => {
                    window.location.href = "{{ route('jadwal.index') }}";
                });
            } else {
                // Fallback jika modal.js tidak memuat customAlert
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

// Event listener (tidak berubah)
document.getElementById('btnSimpanSementara').addEventListener('click', () => saveSchedule(false));
document.getElementById('btnSimpanPermanen').addEventListener('click', () => {
    confirmModal.show(); // <-- Ini sekarang akan berfungsi
});
document.getElementById('confirmPermanentSave').addEventListener('click', () => {
    confirmModal.hide();
    saveSchedule(true);
});
</script>
@endpush