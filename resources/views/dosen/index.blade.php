@extends('layouts.app')

@section('title', 'Data Dosen')

@section('content')
  <div id="title">Data Dosen Pascasarjana</div>
  
  <div class="container search-section">
    
    {{-- FORM DI PERLUKAN untuk Live Search AJAX --}}
    <form action="{{ route('dosen.index') }}" method="GET" class="search-bar" id="searchForm" style="position: relative;">
      
      {{-- Input Pencarian. Padding left disesuaikan dengan posisi ikon. --}}
      <input type="text" 
             name="search" 
             id="searchInput" 
             class="form-control" 
             placeholder="Cari Kode, Nama, atau NIP..."
             value="{{ $search ?? '' }}"
             style="padding-left: 35px;"> 
      
      {{-- Ikon Pencarian. Posisi diatur secara absolute di dalam form relative. --}}
      <i class="bi bi-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); z-index: 10;"></i>

      {{-- Tombol Reset (ikon 'X') DIHILANGKAN sesuai permintaan Anda --}}
      
    </form>

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
        <tbody class="bg-white" id="tableBody">
          {{-- Menggunakan view parsial yang berisi baris-baris tabel --}}
          @include('dosen._table_rows')
        </tbody>
      </table>
    </div>
  </div>

<br><br><br>
@endsection