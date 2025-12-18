@forelse($mahasiswa as $m)
<tr>
    <td class="cell-counter"></td>
    <td>{{ $m->tahun_masuk_string }}</td>
    <td>{{ $m->nim }}</td>
    <td class="text-start">{{ $m->nama }}</td>
    <td>{{ $m->semester }}</td>
    <td>
        <a href="{{ route('krs.editNilai', $m->nim) }}?slug={{ $slug }}" class="btn btn-sm btn-action">
            <i class="bi bi-pencil-fill"></i> Edit
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center py-4">
        <div class="alert alert-secondary text-center" role="alert">
            Data mahasiswa untuk pencarian "{{ $search ?? '' }}" tidak ditemukan dalam angkatan ini.
        </div>
    </td>
</tr>
@endforelse