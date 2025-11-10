@extends('layouts.app')

@section('title', 'Data Matakuliah')

@section('content')
  <div id="title">Data Matakuliah Pascasarjana</div>
  
  <div class="container search-section">
    {{-- <a href="{{ route('matakuliah.importForm') }}" class="btn btn-custom" style="margin: 0; padding: 0 1.5rem; height: 38px; display: flex; align-items: center;">Import</a> --}}

    <div class="search-bar">
      <input type="text" id="searchInput" class="form-control" placeholder="Cari data...">
      <i class="bi bi-search"></i>
    </div>

    <a href="{{ route('matakuliah.create') }}" class="btn btn-custom" id="btnTambah">Tambah</a>
  </div>

  <div class="container mt-3 mb-5">
    <div class="table-responsive">
      <table class="table align-middle text-center custom-table table-matakuliah" id="tabelMatakuliah">
        <thead class="align-middle">
          <tr>
            <th>No</th> 
            
            <th>Kode MK <button class="btn btn-sm btn-sort" data-column="1"></button></th>
            <th>Matakuliah <button class="btn btn-sm btn-sort" data-column="2"></button></th>
            <th>SKS <button class="btn btn-sm btn-sort" data-column="3"></button></th>
            <th>Semester <button class="btn btn-sm btn-sort" data-column="4"></button></th>
            <th>Dosen Pengampu <button class="btn btn-sm btn-sort" data-column="5"></button></th>
          </tr>
        </thead>
        <tbody class="bg-white">
          @foreach($matakuliah as $mk)
          <tr>
            <td class="cell-counter"></td>
            <td>{{ $mk->kode_mk }}</td>
            <td class="text-start">
              <a href="{{ route('matakuliah.show', $mk->kode_mk) }}" class="text-decoration-none text-dark d-inline-flex align-items-center">
                {{ $mk->nama_mk }}
                <i class="bi bi-chevron-right ms-1" style="font-size: 0.9rem; opacity: 0.6;"></i>
              </a>
            </td>
            <td>{{ $mk->sks }}</td>
            <td>{{ $mk->semester }}</td>
            <td class="text-start">
              @forelse($mk->dosen as $d)
                {{ $d->nama }}<br>
              @empty
                -
              @endforelse
              </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <br><br>
@endsection