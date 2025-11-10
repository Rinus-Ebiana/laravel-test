@extends('layouts.app')

@section('title', 'Data Mahasiswa')

@section('content')
  <div id="title">Data Mahasiswa Pascasarjana</div>
  
  <div class="container search-section">
        <div class="search-bar">
      <input type="text" id="searchInput" class="form-control" placeholder="Cari data...">
      <i class="bi bi-search"></i>
    </div>

    <a href="{{ route('mahasiswa.importForm') }}" class="btn btn-custom" style="margin: 0; padding: 0 1.5rem; height: 38px; display: flex; align-items: center;">Import</a>

    <a href="{{ route('mahasiswa.create') }}" class="btn btn-custom" id="btnTambah">Tambah</a>
  </div>

  <div class="container mt-3 mb-5">
    <div class="table-responsive">
      <table class="table align-middle text-center custom-table table-mahasiswa" id="tabelMahasiswa">
        <thead class="align-middle">
          <tr>
            <th>No</th>
            <th>
              Tahun Masuk
              <button class="btn btn-sm btn-sort" data-column="2"></button>
            </th>
            <th>
              Semester
              <button class="btn btn-sm btn-sort" data-column="1"></button>
            </th>
            <th>
              NIM
              <button class="btn btn-sm btn-sort" data-column="2"></button>
            </th>
            <th>
              Nama
              <button class="btn btn-sm btn-sort" data-column="3"></button>
            </th>
            <th>No Telp</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody class="bg-white">
          @foreach($mahasiswa as $m)
          <tr>
            <td class="cell-counter"></td>
            <td class="text-start">{{ $m->tahun_masuk_string }}</td>
            <td>{{ $m->semester }}</td> <td>{{ $m->nim }}</td>
            <td class="text-start">
              <a href="{{ route('mahasiswa.show', ['mahasiswa' => $m->nim]) }}" class="text-decoration-none text-dark d-inline-flex align-items-center">
                {{ $m->nama }}
                <i class="bi bi-chevron-right ms-1" style="font-size: 0.9rem; opacity: 0.6;"></i>
              </a>
            </td>
            <td>{{ $m->no_telp }}</td>
            <td class="text-start">{{ $m->email }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <br>
  <br>
@endsection