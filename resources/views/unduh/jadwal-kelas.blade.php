<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>Unduh Jadwal Kelas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ public_path('css/table-style.css') }}">
  <style>
    /* CSS khusus untuk PDF */
    body { background-color: #fff; }
    .custom-table { border: 2px solid black !important; }
    .custom-table th, .custom-table td { 
        border: 1px solid black !important; 
        padding: 4px 6px; 
        font-size: 10px; 
        vertical-align: middle;
    }
    .custom-table thead th { 
        background-color: #101F6A !important; 
        color: white !important; 
        -webkit-print-color-adjust: exact; 
        print-color-adjust: exact;
    }
    #JudulHalaman { border-bottom: 1px solid #000; display: inline-block; padding-bottom: 3px; }
    h4 { font-size: 14px; }
    h5 { font-size: 16px; }
  </style>
</head>
<body>
  <div id="tableContainer">
    
    @if($schedule)
      <div class="container text-center mt-4 mb-4">
        <h5 id="JudulHalaman" class="fw-semibold">{{ $schedule->nama_jadwal }}</h5>
      </div>

      @foreach($entriesBySemester as $semester => $entries)
      <div class="container mt-2 mb-4" style="max-width:1300px;">
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
                <td class="text-start">{{ $entry->dosen->nama }}</td>
                <td>{{ $entry->hari }}</td>
                <td>{{ $entry->jam_slot }}</td>
                <td>{{ $entry->kode_kelas }}</td>
                <td>{{ $entry->ruang_kelas }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endforeach
      
    @else
      <div class="container text-center mt-5 mb-4">
        <h5 class="fw-semibold">Jadwal Tidak Ditemukan</h5>
      </div>
    @endif

  </div>
</body>
</html>