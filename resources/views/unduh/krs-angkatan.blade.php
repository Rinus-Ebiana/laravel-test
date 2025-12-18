<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>Unduh KRS Angkatan</title>
  {{-- Kita gunakan path absolut untuk CSS di PDF --}}
  <link rel="stylesheet" href="{{ public_path('css/table-style.css') }}">
  <style>
    /* CSS khusus untuk PDF */
    body { background-color: #fff; font-family: sans-serif; }
    .custom-table { border: 2px solid black !important; margin-bottom: 1.5rem; }
    .custom-table th, .custom-table td { 
        border: 1px solid black !important; 
        padding: 4px 6px; 
        font-size: 9px; 
        vertical-align: middle;
    }
    .custom-table thead th { 
        background-color: #101F6A !important; 
        color: white !important; 
        -webkit-print-color-adjust: exact; 
        print-color-adjust: exact;
    }
    h3 { font-size: 16px; }
    h4 { font-size: 14px; }
    /* Memastikan setiap mahasiswa ada di halaman baru */
    .page-break { page-break-after: always; }
  </style>
</head>
<body>

  @foreach($students as $student)
    <div class="container text-center mt-4 mb-3">
      <h3 class="fw-semibold">Kartu Rencana Studi (KRS)</h3>
      <h4 class="fw-normal">{{ $nama_angkatan }}</h4>
    </div>
    
    <table style="font-size: 10px; margin-bottom: 10px;">
        <tr>
            <td style="font-weight: bold; padding-right: 10px;">Nama</td>
            <td>: {{ $student->nama }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding-right: 10px;">NIM</td>
            <td>: {{ $student->nim }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding-right: 10px;">Semester</td>
            <td>: {{ $student->semester }}</td>
        </tr>
    </table>

    <div class="table-responsive">
      <table class="table align-middle text-center custom-table">
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
          @php
            $krsMahasiswa = $krs_map->get($student->nim);
          @endphp
          
          @if($krsMahasiswa)
            @foreach($krsMahasiswa as $krs)
              <tr>
                @if($krs->scheduleEntry)
                  {{-- Ini adalah MK dengan jadwal kelas --}}
                  {{-- PERBAIKAN KRITIS: Gunakan operator Nullsafe (?->) --}}
                  <td>{{ $krs->scheduleEntry->matakuliah?->kode_mk }}</td>
                  <td class="text-start">{{ $krs->scheduleEntry->matakuliah?->nama_mk }}</td>
                  <td>{{ $krs->scheduleEntry->matakuliah?->sks }}</td>
                  <td class="text-start">{{ $krs->scheduleEntry->dosen?->nama }}</td>
                  <td>{{ $krs->scheduleEntry->hari }}</td>
                  <td>{{ $krs->scheduleEntry->jam_slot }}</td>
                  <td>{{ $krs->scheduleEntry->kode_kelas }}</td>
                  <td>{{ $krs->scheduleEntry->ruang_kelas }}</td>
                @elseif($krs->matakuliah)
                  {{-- Ini adalah MK tanpa jadwal (Tesis, dll) --}}
                  <td>{{ $krs->matakuliah->kode_mk }}</td>
                  <td class="text-start">{{ $krs->matakuliah->nama_mk }}</td>
                  <td>{{ $krs->matakuliah->sks }}</td>
                  <td class="text-start">-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                @endif
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="8"><i>Mahasiswa ini belum menyusun KRS.</i></td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
    
    {{-- Pisah halaman untuk mahasiswa berikutnya --}}
    <div class="page-break"></div>
  @endforeach

</body>
</html>