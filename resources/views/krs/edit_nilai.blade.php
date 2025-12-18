@extends('layouts.app')
@section('title', 'Edit Nilai Mahasiswa')

@section('content')
  <div class="container mt-5 mb-4">
    <button class="btn-back d-flex align-items-center gap-1 px-0" onclick="window.history.back()">
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
          </tr>
        </thead>
        <tbody class="bg-white">
          <tr>
            <td>{{ $mahasiswa->semester }}</td>
            <td>{{ $mahasiswa->nim }}</td>
            <td>{{ $mahasiswa->nama }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  
  <div class="container mt-3 d-flex justify-content-between align-items-center">
    {{-- Import Excel Form --}}
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('krs.downloadTemplateNilai') }}" class="btn btn-outline-secondary btn-sm">Download Template</a>
      <form method="POST" action="{{ route('krs.importNilai', $mahasiswa->nim) }}" enctype="multipart/form-data" class="d-inline">
        @csrf
        <input type="file" name="excel_file" accept=".xlsx,.xls" class="form-control form-control-sm d-inline-block" style="width: 200px;" required>
        <button type="submit" class="btn btn-outline-primary btn-sm">Import Excel</button>
      </form>
    </div>

    {{-- Tombol "Susun KRS" dipindah ke halaman angkatan --}}
    <div></div> {{-- Placeholder for spacing --}}
  </div>

  <form method="POST" action="{{ route('krs.storeNilai', $mahasiswa->nim) }}">
    @csrf
    <div class="container mt-3 d-flex justify-content-end align-items-center">
      <button type="submit" id="saveButton" class="btn btn-custom px-4">Simpan Nilai</button>
    </div>

    <div class="container mt-3 mb-5">
      <div class="table-responsive">
        <table class="table align-middle text-center custom-table table-matakuliah" id="tabelMatakuliah">
          <thead class="align-middle">
            <tr>
              <th>Kode MK</th>
              <th>Matakuliah</th>
              <th>SKS</th>
              <th>Semester</th>
              <th>Nilai</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody class="bg-white">
            @foreach($matakuliah as $mk)
            <tr>
              <td>{{ $mk->kode_mk }}</td>
              <td class="text-start">{{ $mk->nama_mk }}</td>
              <td>{{ $mk->sks }}</td>
              <td>{{ $mk->semester }}</td>
              <td class="select-cell">
                @php
                  $currentNilai = $nilaiSudahAda->get($mk->kode_mk) ?? '';
                  $isDisabled = $mahasiswa->semester < $mk->semester;
                @endphp
                <select name="nilai[{{ $mk->kode_mk }}]" class="form-select nilai-select" 
                        {{ $isDisabled ? 'disabled' : '' }}
                        onchange="updateKeterangan(this)">
                  <option value="" @if($currentNilai == '') selected @endif>-</option>
                  <option value="A" @if($currentNilai == 'A') selected @endif>A</option>
                  <option value="AB" @if($currentNilai == 'AB') selected @endif>AB</option>
                  <option value="B" @if($currentNilai == 'B') selected @endif>B</option>
                  <option value="BC" @if($currentNilai == 'BC') selected @endif>BC</option>
                  <option value="C" @if($currentNilai == 'C') selected @endif>C</option>
                  <option value="D" @if($currentNilai == 'D') selected @endif>D</option>
                  <option value="E" @if($currentNilai == 'E') selected @endif>E</option>
                </select>
              </td>
              <td class="keterangan">
                {{-- Keterangan awal --}}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </form>
@endsection

@push('styles')
<style>
  .nilai-select { width: 90px; text-align: center; font-size: 0.95rem; padding: 3px; margin: 0 auto; display: block; }
  td.select-cell { vertical-align: middle !important; }
</style>
@endpush

@push('scripts')
<script>
  function updateKeterangan(selectElement) {
    const selectedValue = selectElement.value;
    const row = selectElement.closest("tr");
    const ketCell = row.querySelector(".keterangan");

    if (!selectedValue || selectedValue === "-") {
      ketCell.textContent = "Belum Menempuh";
    } else if (["A", "AB", "B", "BC", "C"].includes(selectedValue)) {
      ketCell.textContent = "Lulus";
    } else {
      ketCell.textContent = "Tidak Lulus";
    }
  }

  // Inisialisasi semua keterangan saat halaman dimuat
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".nilai-select").forEach(select => {
      // Jika disabled, keterangannya "Belum Saatnya"
      if (select.disabled) {
        const row = select.closest("tr");
        const ketCell = row.querySelector(".keterangan");
        ketCell.textContent = "Belum Saatnya";
      } else {
        updateKeterangan(select);
      }
    });
  });
</script>
@endpush