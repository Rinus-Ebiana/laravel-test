@extends('layouts.app')
@section('title', 'KRS')
@section('content')
  <div id="title">Kartu Rencana Studi (KRS)</div>
  <br><br>

  <div class="container ta-container mb-5">
    
    {{-- $angkatan sekarang adalah objek [ {nama_folder, slug} ] --}}
    @forelse($angkatan as $item)
      <div class="ta-wrapper">
        {{-- Link menggunakan slug baru --}}
        <a href="{{ route('krs.showAngkatan', $item->slug) }}" class="ta-button">{{ $item->nama_folder }}</a>
      </div>
    @empty
      <p class="text-center">Belum ada data mahasiswa.</p>
    @endforelse
  </div>
@endsection

@push('styles')
<style>
  /* Gaya "folder" (tidak berubah) */
  .ta-button {
    display: block; width: 100%; text-align: left;
    padding: 12px 18px; border: 1px solid #000000;
    border-radius: 6px; background-color: #ffffff;
    font-weight: 600; color: #000000;
    transition: all 0.2s ease; text-decoration: none;
  }
  .ta-button:hover {
    background-color: #101F6A; border-color: #101F6A;
    color: #ffffff;
  }
  .ta-container {
    display: grid; grid-template-columns: repeat(2, 1fr);
    gap: 0px 20px; max-width: 1300px;
    margin: 0 auto;
  }
  .ta-wrapper { margin-bottom: 16px; }
</style>
@endpush