@forelse($mahasiswa as $m)
<tr>
    {{-- Total 7 kolom --}}
    <td class="cell-counter"></td> 
    <td class="text-start">{{ $m->tahun_masuk_string }}</td>
    <td>{{ $m->semester }}</td>
    <td>{{ $m->nim }}</td>
    
    <td class="text-start">
        <a href="{{ route('mahasiswa.show', ['mahasiswa' => $m->nim]) }}" class="text-decoration-none text-dark d-inline-flex align-items-center">
            {{ $m->nama }}
            <i class="bi bi-chevron-right ms-1" style="font-size: 0.9rem; opacity: 0.6;"></i>
        </a>
    </td>
    
    <td>
        <form action="{{ route('mahasiswa.updateStatus', $m) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="aktif" {{ $m->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="mengundurkan_diri" {{ $m->status == 'mengundurkan_diri' ? 'selected' : '' }}>Mengundurkan Diri</option>
                <option value="drop_out" {{ $m->status == 'drop_out' ? 'selected' : '' }}>Drop Out</option>
                <option value="lulus" {{ $m->status == 'lulus' ? 'selected' : '' }}>Lulus</option>
            </select>
        </form>
    </td>
    <td>
        <a href="{{ route('mahasiswa.edit', $m) }}" class="btn btn-sm btn-outline-primary me-1">Edit</a>
        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-nim="{{ $m->nim }}" data-nama="{{ $m->nama }}">Hapus</button>
    </td>
</tr>
@empty
<tr>
    {{-- colspan="7" karena total kolom data adalah 7 --}}
    <td colspan="7" class="text-center py-4">
        <div class="alert alert-secondary text-center" role="alert">
            Data mahasiswa untuk pencarian "{{ $search ?? '' }}" tidak ditemukan.
        </div>
    </td>
</tr>
@endforelse