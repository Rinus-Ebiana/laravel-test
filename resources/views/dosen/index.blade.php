@extends('layouts.app')

@section('title', 'Data Dosen')

@section('content')
  <div id="title">Data Dosen Pascasarjana</div>
  
  <div class="container search-section">
    {{-- <a href="{{ route('dosen.importForm') }}" class="btn btn-custom" style="margin: 0; padding: 0 1.5rem; height: 38px; display: flex; align-items: center;">Import</a> --}}

    <div class="search-bar">
      <input type="text" id="searchInput" class="form-control" placeholder="Cari data...">
      <i class="bi bi-search"></i>
    </div>

    <a href="{{ route('dosen.create') }}" class="btn btn-custom" id="btnTambah">Tambah</a>
  </div>

  <div class="container mt-3">
    <div class="table-responsive">
      <table class="table align-middle text-center custom-table table-dosen" id="tabelDosen">
        <thead class="align-middle">
          <tr>
            <th>No</th>
            <th>KD</th>
            <th>
              Nama
              <button class="btn btn-sm btn-sort" data-column="2"></button>
            </th>
            <th>NIP</th>
            <th>No Telp</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody class="bg-white">
          @foreach($dosen as $d)
          <tr>
            <td class="cell-counter"></td> <td>{{ $d->kd }}</td>
            <td class="text-start">
              <a href="{{ route('dosen.show', ['dosen' => $d->kd]) }}" class="text-decoration-none text-dark d-inline-flex align-items-center">
                {{ $d->nama }}
                <i class="bi bi-chevron-right ms-1" style="font-size: 0.9rem; opacity: 0.6;"></i>
              </a>
            </td>
            <td>{{ $d->nip }}</td>
            <td>{{ $d->no_telp }}</td>
            <td class="text-start">{{ $d->email }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

<br><br><br>
@endsection