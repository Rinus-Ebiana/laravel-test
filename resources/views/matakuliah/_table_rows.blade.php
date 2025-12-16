@forelse($matakuliah as $mk)
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
@empty
<tr>
    {{-- colspan="6" karena tabel Matakuliah memiliki 6 kolom --}}
    <td colspan="6" class="text-center py-4">
        <div class="alert alert-secondary text-center" role="alert">
            Data matakuliah untuk pencarian "{{ $search ?? '' }}" tidak ditemukan.
        </div>
    </td>
</tr>
@endforelse