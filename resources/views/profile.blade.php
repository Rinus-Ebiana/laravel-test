@extends('layouts.app')

@section('title', 'Profile')

{{-- 
  Kita tidak perlu lagi CSS custom untuk ikon mata, 
  jadi @push('styles') bisa dihapus. 
--}}

@section('content')
  <div class="container mt-5 mb-4">
    <button class="btn-back d-flex align-items-center gap-1 px-0" onclick="window.history.back()">
      <i class="bi bi-chevron-left" style="font-size: 1.2rem;"></i>
      <span>Kembali</span>
    </button>
  </div>

  <div class="profile-container" style="margin-top: 0;">
    <h6 class="fw-bold mb-5" style="text-decoration: underline; text-underline-offset: 4px;">Profil</h6>

    <form id="profilForm" action="{{ route('profile.update') }}" method="POST">
      @csrf
      @method('PUT')

      <div class="mb-3 row">
        <label for="username" class="col-sm-2 col-form-label">Username</label>
        <div class="col-sm-10">
          <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" 
                 value="{{ old('username', auth()->user()->username) }}" autocomplete="off" required>
          @error('username')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="mb-3 row">
        <label for="passwordBaru" class="col-sm-2 col-form-label">Password Baru</label>
        <div class="col-sm-10 password-wrapper">
          {{-- Tipe input adalah 'password' (standar) --}}
          <input type="password" class="form-control @error('password_baru') is-invalid @enderror" id="passwordBaru" name="password_baru" autocomplete="off" placeholder="Kosongkan jika tidak ingin ganti">
          
          {{-- Ikon mata (<i>) sudah dihapus --}}
          
          <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
          @error('password_baru')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="mb-3 row"> {{-- Mengurangi margin bawah dari mb-5 --}}
        <label for="konfirmasiPassword" class="col-sm-2 col-form-label">Konfirmasi Password</label>
        <div class="col-sm-10 password-wrapper">
          <input type="password" class="form-control" id="konfirmasiPassword" name="password_baru_confirmation" autocomplete="off">
          
          {{-- Ikon mata (<i>) sudah dihapus --}}
        </div>
      </div>
      
      <div class="btn-wrapper">
        <button type="submit" class="btn btn-custom px-4">Simpan</button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
{{-- 
  PERBAIKAN JAVASCRIPT:
  Semua skrip 'setupPasswordInput' yang rumit DIHAPUS.
  Diganti dengan skrip checkbox sederhana dari login.blade.php.
--}}
<script>
    /**
     * FUNGSI BARU: Logika Checkbox dari Halaman Login
     */
    const showPasswordCheckbox = document.getElementById('showPasswordCheckbox');
    const passwordBaruField = document.getElementById('passwordBaru');
    const konfirmasiPasswordField = document.getElementById('konfirmasiPassword');

    if (showPasswordCheckbox && passwordBaruField && konfirmasiPasswordField) {
        showPasswordCheckbox.addEventListener('change', function () {
            // Terapkan tipe baru (text atau password) ke kedua field
            const newType = this.checked ? 'text' : 'password';
            passwordBaruField.type = newType;
            konfirmasiPasswordField.type = newType;
        });
    }

    /**
     * FUNGSI VALIDASI SUBMIT (DIPERBARUI)
     * Sekarang membaca .value secara langsung (tidak perlu 'realPassword')
     */
    document.getElementById('profilForm').addEventListener('submit', async function (e) {
      e.preventDefault(); // Selalu cegah submit default

      const modalEl = document.getElementById('notifModal');
      const modalLabel = document.getElementById('notifModalLabel');
      if (!modalEl || !modalLabel) {
        e.target.submit(); // Lanjutkan submit jika modal tidak ada
        return;
      }
      
      const notifModal = new bootstrap.Modal(modalEl);
      
      // Ambil .value langsung
      const baru = document.getElementById('passwordBaru').value;
      const konfirmasi = document.getElementById('konfirmasiPassword').value;

      // Cek HANYA JIKA password baru diisi
      if (baru.length > 0) {
          const minLength = 8;
          const hasUppercase = /[A-Z]/.test(baru);
          const hasLowercase = /[a-z]/.test(baru);
          const hasNumber = /\d/.test(baru);
          const hasSymbol = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(baru);

          if (baru.length < minLength) {
            modalLabel.textContent = 'Password baru minimal harus 8 karakter.';
            notifModal.show();
            return; // Hentikan submit
          }
          if (!hasUppercase) {
            modalLabel.textContent = 'Password baru harus mengandung setidaknya satu huruf besar.';
            notifModal.show();
            return;
          }
          if (!hasLowercase) {
            modalLabel.textContent = 'Password baru harus mengandung setidaknya satu huruf kecil.';
            notifModal.show();
            return;
          }
          if (!hasNumber) {
            modalLabel.textContent = 'Password baru harus mengandung setidaknya satu angka.';
            notifModal.show();
            return;
          }
          if (!hasSymbol) {
            modalLabel.textContent = 'Password baru harus mengandung setidaknya satu simbol (cth: !@#$).';
            notifModal.show();
            return;
          }
          if (baru !== konfirmasi) {
            modalLabel.textContent = 'Konfirmasi password harus sama dengan password baru.';
            notifModal.show();
            return;
          }
      }

      // Jika validasi lolos (atau password baru kosong)
      e.target.submit(); // Lanjutkan submit form ke controller
    });
</script>
@endpush