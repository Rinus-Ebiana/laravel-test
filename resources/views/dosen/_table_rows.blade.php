{{-- FIX: Menggunakan @forelse untuk menangani kasus data tidak ditemukan --}}
          @forelse($dosen as $d)
          <tr>
            <td class="cell-counter"></td> <td>{{ $d->kd }}</td>
            <td class="text-start">
              <a href="{{ route('dosen.show', ['dosen' => $d->kd]) }}" class="text-decoration-none text-dark d-inline-flex align-items-center">
                {{ $d->nama }}
                <i class="bi bi-chevron-right ms-1" style="font-size: 0.9rem; opacity: 0.6;"></i>
              </a>
            </td>
            <td>{{ $d->nip }}</td>
            <td>{{ $d->no_telp }}</td>
            <td class="text-start">{{ $d->email }}</td>
          </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4">
                @if (isset($search) && $search)
                  Data dosen dengan kata kunci "{{ $search }}" tidak ditemukan.
                @else
                  Data dosen tidak tersedia.
                @endif
              </td>
            </tr>
          @endforelse