<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>Unduh Jadwal Per Dosen</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/table-style.css') }}">
  <style>
    body { background-color: #fff; }
    .custom-table { border: 2px solid black; }
    .custom-table th, .custom-table td { border: 1px solid black; padding: 5px 8px; font-size: 10px; }
    .custom-table thead th { background-color: #101F6A !important; color: white !important; }
    #JudulHalaman { border-bottom: 1px solid #000; display: inline-block; padding-bottom: 3px; }
    .page-break { page-break-after: always; }
  </style>
</head>
<body>
  <div id="tableContainer">
    <div class="container text-center mt-4 mb-4">
      <h5 id="JudulHalaman" class="fw-semibold">{{ $schedule->nama_jadwal }}</h5>
      <h6 class="fw-normal">Jadwal Mengajar Per Dosen</h6>
    </div>

    @foreach($jadwalPerDosen as $kd_dosen => $entries)
      <div class="container mt-4 mb-3" style="max-width:1300px;">
        <h5 class="fw-semibold mb-2">{{ $entries->first()->dosen->nama }} ({{ $kd_dosen }})</h5>
        <div class="table-responsive">
          <table class="table align-middle text-center custom-table table-jadwal">
            <thead>
              <tr>
                <th>Kode MK</th>
                <th>Matakuliah</th>
                <th>SKS</th>
                <th>Semester</th>
                <th>Hari</th>
                <th>Jam</th>
              </tr>
            </thead>
            <tbody class="bg-white">
              @foreach($entries->sortBy('matakuliah.semester') as $entry)
              <tr>
                <td>{{ $entry->matakuliah->kode_mk }}</td>
                <td class="text-start">{{ $entry->matakuliah->nama_mk }}</td>
                <td>{{ $entry->matakuliah->sks }}</td>
                <td>{{ $entry->matakuliah->semester }}</td>
                <td>{{ $entry->hari }}</td>
                <td>{{ $entry->jam_slot }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="page-break"></div>
    @endforeach

  </div>
</body>
</html>