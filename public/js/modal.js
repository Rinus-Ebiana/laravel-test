// Fungsi global untuk menampilkan modal custom (pengganti alert)
function customAlert(message, callback) {
  const modalElement = document.getElementById('customAlert');
  
  // Jika modal belum dimuat, tampilkan pesan error di console
  if (!modalElement) {
    console.error('Modal customAlert belum dimuat di halaman.');
    return;
  }

  const modal = new bootstrap.Modal(modalElement);
  const label = document.getElementById('customAlertLabel');
  const okButton = modalElement.querySelector('.btn');

  label.textContent = message;
  modal.show();

  // Reset event listener agar tidak bertumpuk
  okButton.replaceWith(okButton.cloneNode(true));
  const newOkButton = modalElement.querySelector('.btn');

  // Tutup modal dan jalankan callback
  newOkButton.addEventListener('click', () => {
    modal.hide();
    if (typeof callback === 'function') callback();
  });
}


// Modal Delete

let selectedRow = null;

function initDeleteModal() {
  // Pastikan modal sudah ada di halaman
  const deleteModal = document.getElementById('deleteModal');
  if (!deleteModal) return;

  // Tangkap semua tombol hapus di halaman
  document.querySelectorAll('.icon-btn.delete').forEach((btn) => {
    btn.addEventListener('click', function () {
      selectedRow = this.closest('tr');
    });
  });

  // Aksi konfirmasi hapus
  document.getElementById('confirmDelete').addEventListener('click', function () {
    if (selectedRow) {
      selectedRow.remove();
      selectedRow = null;
    }

    const modal = bootstrap.Modal.getInstance(deleteModal);
    modal.hide();
    
 // Kembali ke halaman sebelumnya setelah hapus
    window.history.back();

  });
}
