// ====== Fungsi pengaturan input password dengan efek ketikan sesaat ======
function setupPasswordInput(id) {
  const input = document.getElementById(id);
  input.realPassword = input.value || "";
  let showMode = false;

  // Event ketika user mengetik password
  input.addEventListener("input", () => {
    const value = input.value;
    const lastChar = value[value.length - 1];

    // Menyesuaikan panjang realPassword
    if (value.length < input.realPassword.length) {
      input.realPassword = input.realPassword.substring(0, value.length);
    } else if (value.length > input.realPassword.length) {
      input.realPassword += lastChar;
    }

    // Tampilkan huruf terakhir sesaat
    if (!showMode) {
      input.value = "•".repeat(input.realPassword.length - 1) + (lastChar || "");
      setTimeout(() => {
        if (!showMode) input.value = "•".repeat(input.realPassword.length);
      }, 500);
    }
  });

  // Event toggle show/hide password
  const toggleIcon = document.querySelector(`.toggle-password[data-target="${id}"]`);
  toggleIcon.addEventListener("click", () => {
    showMode = !showMode;

    // Jika readonly (password lama)
    if (input.hasAttribute("readonly")) {
      if (input.type === "password") {
        input.type = "text";
        toggleIcon.classList.replace("bi-eye", "bi-eye-slash");
      } else {
        input.type = "password";
        toggleIcon.classList.replace("bi-eye-slash", "bi-eye");
      }
      return;
    }

    // Jika editable
    if (showMode) {
      input.value = input.realPassword;
      toggleIcon.classList.replace("bi-eye", "bi-eye-slash");
    } else {
      input.value = "•".repeat(input.realPassword.length);
      toggleIcon.classList.replace("bi-eye-slash", "bi-eye");
    }
  });

  // Mencegah spasi, copy, paste, dll
  input.addEventListener("input", () => {
    input.value = input.value.replace(/\s/g, "");
  });

  ["copy", "cut", "paste", "selectstart", "contextmenu"].forEach(evt => {
    input.addEventListener(evt, e => e.preventDefault());
  });
}

// ====== Inisialisasi semua field password ======
setupPasswordInput("passwordLama");
setupPasswordInput("passwordBaru");
setupPasswordInput("konfirmasiPassword");

// ====== Tombol Batal ======
document.getElementById('btnBatal').addEventListener('click', () => {
  window.history.back();
});

// ====== Validasi dan Simpan Password ======
document.getElementById('profilForm').addEventListener('submit', async function (e) {
  e.preventDefault();

  // Tunggu modal selesai dimuat
  const waitForModal = () => new Promise(resolve => {
    const check = setInterval(() => {
      const modalEl = document.getElementById('notifModal');
      if (modalEl) {
        clearInterval(check);
        resolve(modalEl);
      }
    }, 100);
  });

  const modalEl = await waitForModal();
  const modalLabel = document.getElementById('notifModalLabel');
  const notifModal = new bootstrap.Modal(modalEl);

  const lama = document.getElementById('passwordLama').realPassword;
  const baru = document.getElementById('passwordBaru').realPassword;
  const konfirmasi = document.getElementById('konfirmasiPassword').realPassword;

  // ====== Validasi ======
  if (!baru || !konfirmasi) {
    modalLabel.textContent = 'Password baru dan konfirmasi password tidak boleh kosong.';
    notifModal.show();
    return;
  }

  if (baru.length < 6 || konfirmasi.length < 6) {
    modalLabel.textContent = 'Password baru dan konfirmasi password minimal 6 karakter.';
    notifModal.show();
    return;
  }

  if (baru !== konfirmasi) {
    modalLabel.textContent = 'Konfirmasi password harus sama dengan password baru.';
    notifModal.show();
    return;
  }

  if (baru === lama) {
    modalLabel.textContent = 'Password baru tidak boleh sama dengan password lama.';
    notifModal.show();
    return;
  }

  // ====== Jika valid ======
  modalLabel.textContent = 'Password berhasil diperbarui.';
  notifModal.show();
  this.reset();

  // Kembali ke halaman sebelumnya setelah modal ditutup
  modalEl.addEventListener('hidden.bs.modal', () => {
    window.history.back();
  }, { once: true });
});
