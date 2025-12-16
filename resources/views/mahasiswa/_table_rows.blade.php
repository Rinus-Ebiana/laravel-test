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
    
    <td>{{ $m->no_telp }}</td>
    <td class="text-start">{{ $m->email }}</td>
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