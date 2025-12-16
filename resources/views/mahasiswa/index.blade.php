@extends('layouts.app')

@section('title', 'Data Mahasiswa')

@section('content')
  <div id="title">Data Mahasiswa Pascasarjana</div>
  
  <div class="container search-section">
    
    {{-- FORM dengan ID untuk Live Search AJAX --}}
    <form action="{{ route('mahasiswa.index') }}" method="GET" class="search-bar" id="searchForm" style="position: relative;">
      
      {{-- Input Pencarian. Placeholder diperbarui --}}
      <input type="text" 
             name="search" 
             id="searchInput" 
             class="form-control" 
             placeholder="Cari NIM, Nama, Tahun Masuk, atau Semester..."
             value="{{ $search ?? '' }}"
             style="padding-left: 35px;"> 
      
      {{-- Ikon Pencarian. --}}
      <i class="bi bi-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); z-index: 10;"></i>

    </form>
    
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
        {{-- FIX KRITIS: Tambahkan ID tableBody dan gunakan @include view parsial --}}
        <tbody class="bg-white" id="tableBody"> 
          @include('mahasiswa._table_rows')
        </tbody>
      </table>
    </div>
  </div>

  <br>
  <br>
@endsection