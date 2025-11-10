<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#101F6A; height:70px;">
  <div class="container-fluid h-100 d-flex justify-content-between align-items-center" style="max-width:1350px; margin:0 auto;">
    
    <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none text-white h-100">
      <img src="{{ asset('assets/logoputih.png') }}" alt="Logo" class="me-2" style="height:60px; object-fit:contain;">
      <span class="navbar-brand fw-bold fs-7 text-uppercase mb-0 text-white">
        Sistem Perwalian Pascasarjana ITB STIKOM BALI
      </span>
    </a>
    <div style="padding-right:20px;">
      <i class="bi bi-person fs-3 text-white" id="userIcon" style="cursor:pointer;"></i>
    </div>
  </div>
</nav>

<div id="userPopup" class="shadow-sm p-3 bg-white rounded d-none position-absolute" 
     style="top:80px; width:165px;">
  <div class="text-center">
    <div class="profile-icon"><i class="bi bi-person-circle fs-1"></i></div>
    <span class="fw-semibold d-block">@auth(){{ auth()->user()->username }}@endauth</span>
    
    <button id="btnProfile" class="btn btn-custom w-100 mb-2">Profil</button>
    
    <form id="logoutForm" action="{{ route('logout') }}" method="POST">
        @csrf
        <button id="btnLogout" type="submit" class="btn btn-danger w-100">Log Out</button>
    </form>
  </div>
</div>

@push('scripts')
<script>
// Skrip untuk tombol profile di navbar
// (Memastikan ini berjalan setelah main.js)
document.addEventListener('DOMContentLoaded', function() {
    const btnProfile = document.getElementById('btnProfile');
    if(btnProfile) {
        btnProfile.addEventListener('click', () => {
            window.location.href = "{{ route('profile.edit') }}";
        });
    }

    // Hapus event listener logout default dari main.js jika ada
    const btnLogout = document.getElementById('btnLogout');
    if(btnLogout) {
      // Mencegah event listener ganda dari main.js
      btnLogout.addEventListener('click', (e) => {
          e.preventDefault(); // Hentikan klik default jika ada
          e.stopPropagation(); // Hentikan event bubbling
          
          // Submit form logout
          const logoutForm = document.getElementById('logoutForm');
          if(logoutForm) {
            logoutForm.submit();
          } else {
            // Fallback jika form tidak ditemukan (meskipun seharusnya ada)
            console.error('Logout form not found!');
            // Jika Anda masih menyimpan logika logout di main.js, 
            // Anda bisa memanggilnya di sini, tapi idealnya tidak.
          }
      });
    }

    // Hapus juga event listener dari main.js jika ia menambahkannya
    // (Ini bagian yang agak rumit, idealnya main.js tidak hardcode #btnLogout)
    // Jika main.js Anda *hanya* berisi loadHTML, initUserPopup, initNavButtons, dll
    // maka kita perlu memodifikasi initUserPopup di main.js
    // atau menimpanya di sini.

    // Cara paling aman: Pastikan main.js Anda tidak menangani #btnLogout
    // Biarkan #btnLogout di navbar.blade.php ini yang menanganinya.
});
</script>
@endpush