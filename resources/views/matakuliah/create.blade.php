@extends('layouts.app')

@section('title', 'Tambah Data Matakuliah')

@push('styles')
  {{-- Tambahkan CSS untuk TomSelect (dropdown pencarian) --}}
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
@endpush

@section('content')
  <div class="profile-container">
    <h6 class="fw-bold mb-5" style="text-decoration: underline; text-underline-offset: 4px;">Tambah Data Matakuliah</h6>

    <form id="tambahMatakuliahForm" method="POST" action="{{ route('matakuliah.store') }}">
      @csrf

      <div class="mb-3 row">
        <label for="kode_mk" class="col-sm-2 col-form-label">Kode MK</label>
        <div class="col-sm-10">
          <input type="text" id="kode_mk" name="kode_mk" class="form-control @error('kode_mk') is-invalid @enderror" value="{{ old('kode_mk') }}" required>
          @error('kode_mk') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mb-3 row">
        <label for="nama_mk" class="col-sm-2 col-form-label">Nama Matakuliah</label>
        <div class="col-sm-10">
          <input type="text" id="nama_mk" name="nama_mk" class="form-control @error('nama_mk') is-invalid @enderror" value="{{ old('nama_mk') }}" required>
          @error('nama_mk') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mb-3 row">
        <label for="sks" class="col-sm-2 col-form-label">SKS</label>
        <div class="col-sm-10">
          <input type="number" id="sks" name="sks" class="form-control @error('sks') is-invalid @enderror" value="{{ old('sks') }}" required>
          @error('sks') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mb-3 row">
        <label for="semester" class="col-sm-2 col-form-label">Semester</label>
        <div class="col-sm-10">
          <input type="number" id="semester" name="semester" class="form-control @error('semester') is-invalid @enderror" value="{{ old('semester') }}" required>
          @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mb-5 row">
        <label for="select-dosen" class="col-sm-2 col-form-label">Dosen Pengampu</label>
        <div class="col-sm-10">
          <select id="select-dosen" name="dosen_kd[]" class="@error('dosen_kd') is-invalid @enderror" multiple>
            @foreach($dosen as $d)
              <option value="{{ $d->kd }}"
                {{ ( in_array($d->kd, old('dosen_kd', [])) ) ? 'selected' : '' }}
              >
                {{ $d->nama }} ({{ $d->kd }})
              </option>
            @endforeach
          </select>
          @error('dosen_kd') <div class="invalid-feedback">{{ $message }}</div> @enderror
          <div class="form-text mt-2">
            Bisa ketik untuk mencari dan memilih lebih dari satu dosen.
          </div>
        </div>
      </div>
      <div class="btn-wrapper">
        <button type="button" class="btn btn-custom px-4 me-1" id="btnBatal">Batal</button>
        <button type="submit" class="btn btn-custom px-4">Simpan</button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
  {{-- Tambahkan JS untuk TomSelect --}}
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
  <script>
    // Inisialisasi TomSelect pada <select>
    document.addEventListener("DOMContentLoaded", () => {
      new TomSelect("#select-dosen",{
        plugins: ['remove_button'],
        create: false,
        sortField: {
          field: "text",
          direction: "asc"
        }
      });
    });

    // Tombol Batal kembali ke halaman sebelumnya
    document.getElementById('btnBatal').addEventListener('click', () => {
      window.history.back();
    });
  </script>
@endpush