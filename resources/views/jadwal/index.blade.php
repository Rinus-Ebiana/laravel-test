@extends('layouts.app')

@section('title', 'Jadwal Perkuliahan')

@section('content')

  @if($schedule)
    <div id="title">{{ $schedule->nama_jadwal }}</div>
  @else
    <div id="title">Jadwal Perkuliahan</div>
  @endif

  <div class="container mt-4 mb-4 d-flex justify-content-end gap-2" style="max-width:1300px;">
    @if($schedule && !$schedule->is_permanent)
      <button class="btn btn-custom px-4" onclick="window.location.href='{{ route('jadwal.edit', $schedule->id) }}'">Edit</button>
    @else
      <button class="btn btn-custom px-4" disabled>Edit</button>
    @endif
    
    <button class="btn btn-custom px-4" onclick="window.location.href='{{ route('jadwal.history') }}'">History</button>
    <button class="btn btn-custom px-4" onclick="window.location.href='{{ route('jadwal.create') }}'">Buat</button>
  </div>

  <div class="container mt-3">
    @if($schedule)
      <div class="table-responsive">
        <table class="table align-middle text-center custom-table table-jadwal" id="tabelJadwal">
          <thead class="align-middle">
            <tr>
              <th>Kode MK</th>
              <th>Matakuliah</th>
              <th>SKS</th>
              <th>Semester</th>
              <th>Dosen Pengampu</th>
              <th>Hari</th>
              <th>Jam</th>
            </tr>
          </thead>
          <tbody class="bg-white">
            @foreach($schedule->entries->sortBy('matakuliah.semester') as $entry)
              <tr>
                <td>{{ $entry->matakuliah->kode_mk }}</td>
                <td class="text-start">{{ $entry->matakuliah->nama_mk }}</td>
                <td>{{ $entry->matakuliah->sks }}</td>
                <td>{{ $entry->matakuliah->semester }}</td>
                <td class="text-start">{{ $entry->dosen->nama }} ({{ $entry->dosen->kd }})</td>
                <td>{{ $entry->hari }}</td>
                <td>{{ $entry->jam_slot }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="text-center" style="margin-top: 5rem;">
        <h5>Belum ada jadwal aktif.</h5>
        <p>Silakan klik tombol "Buat" untuk memulai penjadwalan baru.</p>
      </div>
    @endif
  </div>

  @if($schedule)
    <div class="container mt-4 mb-5 d-flex justify-content-end gap-2" style="max-width:1300px;">
      <button class="btn btn-custom px-4" onclick="window.location.href='{{ route('jadwal.downloadPerDosen', $schedule->id) }}'">Unduh Per Dosen (PDF)</button>
      <button class="btn btn-custom px-4" onclick="window.location.href='{{ route('jadwal.downloadSemua', $schedule->id) }}'">Unduh Semua (PDF)</button>
      <button class="btn btn-custom px-4" onclick="window.location.href='{{ route('jadwal.downloadPerDosenExcel', $schedule->id) }}'">Unduh Per Dosen (Excel)</button>
      <button class="btn btn-custom px-4" onclick="window.location.href='{{ route('jadwal.downloadSemuaExcel', $schedule->id) }}'">Unduh Semua (Excel)</button>
    </div>
  @endif
@endsection