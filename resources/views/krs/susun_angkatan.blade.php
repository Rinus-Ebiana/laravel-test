@extends('layouts.app')
@section('title', 'Susun KRS Angkatan')

@section('content')
  <div class="container mt-5 mb-4">
    <button class="btn-back d-flex align-items-center gap-1 px-0" onclick="window.history.back()">
      <i class="bi bi-chevron-left" style="font-size: 1.2rem;"></i>
      <span>Kembali</span>
    </button>
  </div>

  <h4 class="fw-semibold mb-3 text-center">Susun KRS Angkatan</h4>

  <form method="POST" action="{{ route('krs.storeAngkatan', $slug) }}">
    @csrf
    <div class="container mt-3" style="max-width:1300px;">
      <div class="table-responsive">
        <table class="table align-middle text-center custom-table table-jadwal border">
          <thead class="align-middle">
            <tr>
              <th rowspan="2" style="min-width: 200px;">Mahasiswa</th>
              <th colspan="5">Hari Reguler (18:00–20:30)</th>
              <th colspan="3">Hari Sabtu</th>
              <th colspan="3">Matakuliah Akhir (Tanpa Jadwal)</th> {{-- BENAR (3) --}}
            </tr>
            <tr>
              <th>Senin</th>
              <th>Selasa</th>
              <th>Rabu</th>
              <th>Kamis</th>
              <th>Jumat</th>
              <th>13:00–15:30</th>
              <th>15:30–18:00</th>
              <th>18:00–20:30</th>
              
              {{-- BENAR (Hanya 3 kolom) --}}
              <th>Publikasi (S3)</th>
              <th>Seminar Tesis (S3)</th>
              <th>Tesis (S4)</th>
            </tr>
          </thead>
          <tbody class="bg-white">
            
            @foreach($students as $student)
              @php
                // Ambil data penting untuk mahasiswa ini
                $mahasiswaSemester = $student->semester;
                $nilaiMahasiswa = $grades_map->get($student->nim) ?? collect();
                $krsTersimpan = $krs_map->get($student->nim);
                
                // Cek status lulus semua MK prasyarat (per mahasiswa)
                $lulusSemuaPraTesis = true;
                foreach ($mk_pra_tesis as $kode_mk) { // $mk_pra_tesis dari controller
                    $nilai = $nilaiMahasiswa->get($kode_mk);
                    if (!in_array($nilai, ['A', 'AB', 'B', 'BC', 'C'])) {
                        $lulusSemuaPraTesis = false;
                        break;
                    }
                }
              @endphp

            <tr>
              <td class="text-start">{{ $student->nama }}</td>

              {{-- Loop untuk SLOT HARI/JAM --}}
              @foreach($slots as $slot)
                <td>
                  <select name="krs[{{ $student->nim }}][{{ $slot }}]" class="form-select form-select-sm" style="min-width: 120px;">
                    <option value="">- Pilih Kelas -</option>
                    
                    @if(isset($classes_by_slot[$slot]))
                      @foreach($classes_by_slot[$slot] as $class)
                        @php
                          $mk = $class->matakuliah;

                          // === PERBAIKAN LOGIKA ADA DI SINI ===
                          // 1. Cek apakah semester MK <= semester mahasiswa
                          if ($mk->semester <= $mahasiswaSemester) {
                              // 2. Cek apakah mahasiswa sudah lulus MK ini
                              $nilai = $nilaiMahasiswa->get($mk->kode_mk) ?? null;
                              $is_passed = in_array($nilai, ['A', 'AB', 'B', 'BC', 'C']);
                              
                              // 3. Cek KRS tersimpan
                              $isSelected = $krsTersimpan && $krsTersimpan->contains('schedule_entry_id', $class->id);

                              // 4. Tampilkan HANYA JIKA belum lulus
                              if (!$is_passed) {
                                  echo "<option value='{$class->id}' ".($isSelected ? 'selected' : '').">
                                          {$class->kode_kelas} ({$mk->kode_mk})
                                        </option>";
                              }
                          }
                        @endphp
                      @endforeach
                    @endif
                  </select>
                </td>
              @endforeach

              {{-- Loop untuk MK TANPA SLOT (TESIS, DLL) --}}
              @foreach($mk_tanpa_slot as $mk)
                <td>
                  @php
                    $kode_mk = $mk->kode_mk;
                    
                    // === PERBAIKAN LOGIKA ADA DI SINI ===
                    // 1. Cek apakah semester MK <= semester mahasiswa
                    if ($mk->semester <= $mahasiswaSemester) {
                        $nilai = $nilaiMahasiswa->get($kode_mk) ?? null;
                        $is_passed = in_array($nilai, ['A', 'AB', 'B', 'BC', 'C']);
                        $isChecked = $krsTersimpan && $krsTersimpan->contains('matakuliah_kode_mk', $kode_mk);

                        // 2. Jika belum lulus
                        if (!$is_passed) {
                            // 3. Terapkan aturan khusus Tesis
                            if ($kode_mk == 'M1241109' && !$lulusSemuaPraTesis) {
                                echo '<input type="checkbox" class="form-check-input" disabled>';
                                echo '<small class="text-danger d-block" style="font-size: 0.7rem;">(Belum Lulus Semua MK)</small>';
                            } else {
                                echo "<input class='form-check-input' type='checkbox' 
                                             name='krs_mk[{$student->nim}][]' 
                                             value='{$kode_mk}'
                                             ".($isChecked ? 'checked' : '').">";
                            }
                        } else {
                            echo '<span class="text-success small">Lulus</span>';
                        }
                    
                    } else {
                        // Jika semester belum tercapai
                        echo '<span class="text-muted small" title="Semester belum tercapai">-</span>';
                    }
                  @endphp
                </td>
              @endforeach
              
            </tr>
            @endforeach

          </tbody>
        </table>
      </div>
    </div>

    <div class="container mt-4 mb-5 d-flex justify-content-end gap-2" style="max-width:1300px;">
      <button type="submit" class="btn btn-custom px-4">Simpan KRS Angkatan</button>
    </div>
  </form>
@endsection