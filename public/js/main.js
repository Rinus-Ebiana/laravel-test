// File: main.js

// FIX KRITIS: Global Execution Flag. Mencegah kode dijalankan dua kali jika file di-load dua kali.
if (window.__MAIN_JS_INITIALIZED) {
    // Jika flag sudah ada, hentikan eksekusi script ini (kecuali definisi fungsi)
    console.warn("main.js terdeteksi dimuat lebih dari sekali. Menghentikan inisialisasi ganda.");
} else {
    window.__MAIN_JS_INITIALIZED = true;
}


// Fungsi Pop Up User Profile (TIDAK BERUBAH)
function initUserPopup() {
  const userIcon = document.getElementById('userIcon');
  const userPopup = document.getElementById('userPopup');

  if (!userIcon || !userPopup) {
    return;
  }

  userIcon.addEventListener('click', (e) => {
    e.stopPropagation();
    const isVisible = userPopup.classList.contains('show');
    userPopup.classList.remove('show');
    if (isVisible) return;

    const rect = userIcon.getBoundingClientRect();
    const top = rect.bottom + window.scrollY + 10;
    const left = rect.right - userPopup.offsetWidth + window.scrollX - 100;

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
}

// Fungsi Sorting (Fungsi updateCellCounters dan semua panggilannya DIHAPUS)
function initSearchAndSort() {
  
  // Helper untuk membersihkan dan mengambil nilai teks (sangat robust)
  const cleanText = (cell) => {
      if (!cell) return '';
      let text = cell.textContent || ''; 
      text = text.replace(/\xA0/g, ' ').replace(/\s+/g, ' ').trim(); 
      return text;
  };

  // 2. Bagian Sorting
  document.querySelectorAll('.btn-sort').forEach(button => {
    // Hapus listener lama untuk mencegah double listener pada AJAX refresh
    button.removeEventListener('click', button.clickHandler); 

    const clickHandler = () => {
      const table = button.closest('table');
      const tbody = table.querySelector('tbody');
      
      const columnIndex = parseInt(button.dataset.column); 
      const isAscending = !button.classList.contains('asc');

      table.querySelectorAll('.btn-sort').forEach(btn => btn.classList.remove('active', 'asc', 'desc'));
      button.classList.add('active', isAscending ? 'asc' : 'desc');

      const rows = Array.from(tbody.querySelectorAll('tr'));

      rows.sort((a, b) => {
        let comparison = 0;

        // --- LOGIKA SORTING STANDAR (Numerik atau Teks) ---
        const textA = cleanText(a.children[columnIndex]);
        const textB = cleanText(b.children[columnIndex]);

        const valA = parseFloat(textA);
        const valB = parseFloat(textB);

        const isNumeric = !isNaN(valA) && isFinite(valA) && !isNaN(valB) && isFinite(valB);

        if (isNumeric) {
            comparison = valA - valB;
        } else {
            comparison = textA.localeCompare(textB, 'id', { sensitivity: 'base' });
        }

        // Terapkan arah sorting
        return isAscending ? comparison : comparison * -1;
      }
      );

      // Sisipkan baris yang sudah diurutkan kembali ke tbody
      rows.forEach(row => tbody.appendChild(row));
      
      // updateCellCounters() DIHAPUS DI SINI. Penomoran dikelola oleh View.
    };

    button.addEventListener('click', clickHandler);
    button.clickHandler = clickHandler; // Simpan reference handler
  });
}

// ================== EKSEKUSI SAAT HALAMAN DIMUAT ==================
document.addEventListener('DOMContentLoaded', function() {
  
  // Jika flag tidak diinisialisasi (berarti ini adalah script pertama), baru jalankan
  if (window.__MAIN_JS_INITIALIZED) {
    
    // 1. Jalankan fungsi popup user
    initUserPopup();

    // updateCellCounters() DIHAPUS DI SINI. Penomoran dikelola oleh View.

    // 2. Jalankan fungsi search dan sort (hanya inisialisasi listener)
    initSearchAndSort();

    // 3. Tambahkan class 'loaded' ke body
    document.body.classList.add('loaded');

    // 4. Logic Live Search dengan Debounce
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('tableBody');
    const searchForm = document.getElementById('searchForm'); 

    if (searchInput && tableBody && searchForm) {
        let timeout = null;
        let lastSearchValue = searchInput.value; 
        
        const performSearch = function (searchValue) {
            
            if (searchValue === lastSearchValue) {
                return;
            }
            lastSearchValue = searchValue;
            
            const url = searchForm.getAttribute('action');
            const fetchUrl = `${url}?search=${encodeURIComponent(searchValue)}&ajax=1`; 
            
            fetch(fetchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                tableBody.innerHTML = html; 
                
                // updateCellCounters() DIHAPUS DI SINI.
                initSearchAndSort(); 
            })
            .catch(error => {
                console.error('Error saat melakukan pencarian AJAX:', error);
            });
        };

        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            const currentCursorPosition = searchInput.selectionStart;

            timeout = setTimeout(function () {
                performSearch(searchInput.value);
                
                searchInput.focus();
                searchInput.setSelectionRange(currentCursorPosition, currentCursorPosition);
                
            }, 300); 

        });
        
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
            performSearch(searchInput.value);
        });
    }
  }
});