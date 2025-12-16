@extends('layouts.app')

@section('title', 'Data Matakuliah')

@section('content')
  <div id="title">Data Matakuliah Pascasarjana</div>
  
  <div class="container search-section">
    {{-- FIX: Mengganti div.search-bar menjadi <form> dengan ID dan style untuk layout --}}
    <form action="{{ route('matakuliah.index') }}" method="GET" class="search-bar" id="searchForm" style="position: relative;">
      
      {{-- Input Pencarian. Placeholder diperbarui --}}
      <input type="text" 
             name="search" 
             id="searchInput" 
             class="form-control" 
             placeholder="Cari Kode, Nama, SKS, Semester, atau Dosen..."
             value="{{ $search ?? '' }}"
             style="padding-left: 35px;"> 
      
      {{-- Ikon Pencarian. Posisi diatur secara absolute. --}}
      <i class="bi bi-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); z-index: 10;"></i>

    </form>
    
    {{-- <a href="{{ route('matakuliah.importForm') }}" class="btn btn-custom" style="margin: 0; padding: 0 1.5rem; height: 38px; display: flex; align-items: center;">Import</a> --}}

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
        {{-- FIX KRITIS: Tambahkan ID tableBody dan gunakan @include view parsial --}}
        <tbody class="bg-white" id="tableBody"> 
          @include('matakuliah._table_rows')
        </tbody>
      </table>
    </div>
  </div>
  <br><br>
@endsection