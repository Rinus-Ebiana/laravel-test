// Fungsi Pop Up User Profile
function initUserPopup() {
  const userIcon = document.getElementById('userIcon');
  const userPopup = document.getElementById('userPopup');
  const btnProfile = document.getElementById('btnProfile');
  const btnLogout = document.getElementById('btnLogout');

  if (!userIcon || !userPopup) {
    // console.error("Elemen popup user tidak ditemukan.");
    return;
  }

  userIcon.addEventListener('click', (e) => {
    e.stopPropagation();
    const isVisible = userPopup.classList.contains('show');
    userPopup.classList.remove('show');
    if (isVisible) return;

    const rect = userIcon.getBoundingClientRect();
    const top = rect.bottom + window.scrollY + 10;
    const left = rect.right - userPopup.offsetWidth + window.scrollX - 100; // Penyesuaian offset

    userPopup.style.position = 'absolute';
    userPopup.style.top = `${top}px`;
    userPopup.style.left = `${left}px`;
    userPopup.classList.add('show');
  });

  document.addEventListener('click', (e) => {
    if (userPopup && !userPopup.contains(e.target) && !userIcon.contains(e.target)) {
      userPopup.classList.remove('show');
    }
  });

  // Navigasi ke halaman profil (Biarkan ini dikelola oleh Blade)
  // if (btnProfile) {
  //   btnProfile.addEventListener('click', () => {
  //     window.location.href = "profile.html"; // Dikelola oleh Blade/HTML
  //   });
  // }

  // Logout (Biarkan ini dikelola oleh form Blade)
  // if (btnLogout) {
  //   btnLogout.addEventListener('click', () => {
  //     // ... Dikelola oleh Form Blade
  //   });
  // }
}

// Fungsi Sorting dan Search (Tidak berubah, hanya dipindahkan)
function initSearchAndSort() {
  // --- Bagian Search Bar ---
  // const container = document.getElementById('searchSection');
  // if (container) {
  //   const tableId = container.getAttribute('data-target');
  //   const addPage = container.getAttribute('data-add'); // Ini adalah URL

  //   let htmlContent = `
  //     <div class="container search-section">
  //       <div class="search-bar">
  //         <input type="text" id="searchInput" class="form-control" placeholder="Cari data...">
  //         <i class="bi bi-search"></i>
  //       </div>
  //   `;
  //   if (addPage && addPage !== "none") {
  //     htmlContent += `<button class="btn btn-custom" id="btnTambah">Tambah</button>`;
  //   }
  //   htmlContent += `</div>`;
  //   container.innerHTML = htmlContent;

  //   const btnTambah = document.getElementById('btnTambah');
  //   if (btnTambah) {
  //     // Sekarang kita gunakan URL dari 'data-add'
  //     btnTambah.addEventListener('click', () => window.location.href = addPage);
  //   }

  //   const searchInput = document.getElementById('searchInput');
  //   if (searchInput && tableId) {
  //     searchInput.addEventListener('input', () => {
  //       const filter = searchInput.value.toLowerCase();
  //       const rows = document.querySelectorAll(`#${tableId} tbody tr`);
  //       rows.forEach(row => {
  //         const text = row.textContent.toLowerCase();
  //         row.style.display = text.includes(filter) ? '' : 'none';
  //       });
  //     });
  //   }
  // }

  // --- Bagian Sorting ---
  document.querySelectorAll('.btn-sort').forEach(button => {
    button.addEventListener('click', () => {
      const table = button.closest('table');
      const tbody = table.querySelector('tbody');
      const columnIndex = parseInt(button.dataset.column);
      const isAscending = !button.classList.contains('asc');

      table.querySelectorAll('.btn-sort').forEach(btn => btn.classList.remove('active', 'asc', 'desc'));
      button.classList.add('active', isAscending ? 'asc' : 'desc');

      const rows = Array.from(tbody.querySelectorAll('tr'));

      rows.sort((a, b) => {
        const A = a.children[columnIndex].innerText.trim().toLowerCase();
        const B = b.children[columnIndex].innerText.trim().toLowerCase();
        return isAscending ? A.localeCompare(B) : B.localeCompare(A);
      });

      rows.forEach(row => tbody.appendChild(row));
    });
  });
}

// ================== EKSEKUSI SAAT HALAMAN DIMUAT ==================
document.addEventListener('DOMContentLoaded', function() {
  
  // 1. Jalankan fungsi popup user
  initUserPopup();

  // 2. Jalankan fungsi search dan sort
  initSearchAndSort();

  // 3. Tambahkan class 'loaded' ke body
  document.body.classList.add('loaded');

  // (Fungsi initNavButtons() tidak diperlukan lagi, diganti oleh Blade)
});