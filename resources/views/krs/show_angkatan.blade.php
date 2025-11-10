@extends('layouts.app')
@section('title', 'Detail KRS Angkatan')

@section('content')
  <div class="container mt-5">
    <button class="btn-back d-flex align-items-center gap-1 px-0" onclick="window.location.href='{{ route('krs.index') }}'">
      <i class="bi bi-chevron-left" style="font-size: 1.2rem;"></i>
      <span>Kembali</span>
    </button>
  </div>

  <h3 class="text-center fw-semibold mt-4 mb-4">{{ $angkatan }}</h3>

  <div class="container search-section">
    <div class="search-bar">
      <input type="text" id="searchInput" class="form-control" placeholder="Cari mahasiswa...">
      <i class="bi bi-search"></i>
    </div>
    
    <a href="{{ route('krs.downloadAngkatan', $slug) }}" class="btn btn-custom px-4" style="margin-top: 0; height: 38px;">Unduh KRS</a>
    <a href="{{ route('krs.susunAngkatan', $slug) }}" class="btn btn-custom px-4" style="margin-top: 0; height: 38px;">Susun KRS</a>
  </div>

  <div class="container mt-3 mb-5">
    <div class="table-responsive">
      <table class="table align-middle text-center custom-table table-mahasiswa" id="tabelMahasiswa">
        <thead class="align-middle">
          <tr>
            <th>No</th>
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
          @foreach($mahasiswa as $mhs)
          <tr>
            <td class="cell-counter"></td>
            <td>{{ $mhs->semester }}</td>
            <td>{{ $mhs->nim }}</td>
            <td class="text-start">
              <a href="{{ route('krs.editNilai', $mhs->nim) }}" class="text-decoration-none text-dark d-inline-flex align-items-center">
                {{ $mhs->nama }}
                <i class="bi bi-chevron-right ms-1" style="font-size: 0.9rem; opacity: 0.6;"></i>
              </a>
            </td>
            <td>{{ $mhs->no_telp }}</td>
            <td class="text-start">{{ $mhs->email }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection