@extends('layouts.app')

@section('title', 'Edit Data Matakuliah')

@push('styles')
  {{-- Tambahkan CSS untuk TomSelect (dropdown pencarian) --}}
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
@endpush

@section('content')
  <div class="profile-container">
    <h6 class="fw-bold mb-5" style="text-decoration: underline; text-underline-offset: 4px;">Edit Data Matakuliah</h6>

    <form id="editMatakuliahForm" method="POST" action="{{ route('matakuliah.update', $matakuliah->kode_mk) }}">
      @csrf
      @method('PUT')

      <div class="mb-3 row">
        <label for="kode_mk" class="col-sm-2 col-form-label">Kode MK</label>
        <div class="col-sm-10">
          {{-- Kode MK dibuat readonly karena ini adalah Primary Key --}}
          <input type="text" id="kode_mk" name="kode_mk" class="form-control" value="{{ $matakuliah->kode_mk }}" readonly style="background-color: #e9ecef;">
        </div>
      </div>

      <div class="mb-3 row">
        <label for="nama_mk" class="col-sm-2 col-form-label">Nama Matakuliah</label>
        <div class="col-sm-10">
          <input type="text" id="nama_mk" name="nama_mk" class="form-control @error('nama_mk') is-invalid @enderror" value="{{ old('nama_mk', $matakuliah->nama_mk) }}" required>
          @error('nama_mk')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="mb-3 row">
        <label for="sks" class="col-sm-2 col-form-label">SKS</label>
        <div class="col-sm-10">
          <input type="number" id="sks" name="sks" class="form-control @error('sks') is-invalid @enderror" value="{{ old('sks', $matakuliah->sks) }}" required>
          @error('sks') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mb-3 row">
        <label for="semester" class="col-sm-2 col-form-label">Semester</label>
        <div class="col-sm-10">
          <input type="number" id="semester" name="semester" class="form-control @error('semester') is-invalid @enderror" value="{{ old('semester', $matakuliah->semester) }}" required>
          @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mb-5 row">
        <label for="select-dosen" class="col-sm-2 col-form-label">Dosen Pengampu</label>
        <div class="col-sm-10">
          {{-- Dropdown pencarian canggih --}}
          <select id="select-dosen" name="dosens[]" class="@error('dosens') is-invalid @enderror" multiple>
            {{-- Loop semua dosen --}}
            @foreach ($dosens as $dosen)
              <option value="{{ $dosen->kd }}"
                {{-- Cek apakah dosen ini ada di 'selectedDosens' ATAU di 'old' input --}}
                {{ ( in_array($dosen->kd, old('dosens', $selectedDosens)) ) ? 'selected' : '' }}
              >
                {{ $dosen->nama }} ({{ $dosen->kd }})
              </option>
            @endforeach
          </select>
          <div class="form-text">Bisa ketik untuk mencari dan memilih lebih dari satu dosen.</div>
          @error('dosens')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="btn-wrapper">
        {{-- Tombol Batal sekarang kembali ke halaman show --}}
        <a href="{{ route('matakuliah.show', $matakuliah->kode_mk) }}" class="btn btn-custom px-4 me-1" id="btnBatal">Batal</a>
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
  </script>
@endpush