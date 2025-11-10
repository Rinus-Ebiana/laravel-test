<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KrsController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MatakuliahController;
use App\Http\Controllers\ProfileController;
// (Tambahkan controller lain jika ada)
// use App\Http\Controllers\KrsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rute root (/) sekarang otomatis mengarah ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// === RUTE AUTENTIKASI (LOGIN/LOGOUT) ===

// GET /login -> Menampilkan form login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// POST /login -> Memproses data login
Route::post('/login', [AuthController::class, 'login']);

// POST /logout -> Memproses logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// === RUTE APLIKASI (WAJIB LOGIN) ===
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Modul Resource (Otomatis membuat rute index, create, store, show, edit, update, destroy)
    Route::resource('dosen', DosenController::class);
    Route::get('/mahasiswa/import', [MahasiswaController::class, 'showImportForm'])->name('mahasiswa.importForm');
    Route::post('/mahasiswa/import', [MahasiswaController::class, 'storeImport'])->name('mahasiswa.storeImport');
    Route::resource('mahasiswa', MahasiswaController::class);
    Route::resource('matakuliah', MatakuliahController::class);

    // ===== RUTR JADWAL LENGKAP =====
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal/history', [JadwalController::class, 'history'])->name('jadwal.history');
    
    // Create
    Route::get('/jadwal/create', [JadwalController::class, 'create'])->name('jadwal.create');
    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    
    // Edit & Update
    Route::get('/jadwal/{schedule}/edit', [JadwalController::class, 'edit'])->name('jadwal.edit');
    Route::put('/jadwal/{schedule}', [JadwalController::class, 'update'])->name('jadwal.update');

    // Delete
    Route::delete('/jadwal/{schedule}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');

    // Download
    Route::get('/jadwal/{schedule}/download-semua', [JadwalController::class, 'downloadSemua'])->name('jadwal.downloadSemua');
    Route::get('/jadwal/{schedule}/download-dosen', [JadwalController::class, 'downloadPerDosen'])->name('jadwal.downloadPerDosen');

    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    // Rute baru untuk menyimpan data dari form contenteditable
    Route::post('/kelas/update', [KelasController::class, 'update'])->name('kelas.update');
    // Rute baru untuk mengunduh
    Route::get('/kelas/download', [KelasController::class, 'download'])->name('kelas.download');

    Route::get('/krs', [KrsController::class, 'index'])->name('krs.index');
    Route::get('/krs/angkatan/{slug}', [KrsController::class, 'showAngkatan'])->name('krs.showAngkatan');

    // Rute Halaman Nilai (Tetap per mahasiswa)
    Route::get('/krs/mahasiswa/{mahasiswa}/nilai', [KrsController::class, 'editNilai'])->name('krs.editNilai');
    Route::post('/krs/mahasiswa/{mahasiswa}/nilai', [KrsController::class, 'storeNilai'])->name('krs.storeNilai');

    // Rute Susun KRS (BARU - Per Angkatan)
    Route::get('/krs/angkatan/{slug}/susun', [KrsController::class, 'susunAngkatan'])->name('krs.susunAngkatan');
    Route::post('/krs/angkatan/{slug}/susun', [KrsController::class, 'storeAngkatan'])->name('krs.storeAngkatan');

    Route::get('/krs/angkatan/{slug}/download', [KrsController::class, 'downloadAngkatan'])->name('krs.downloadAngkatan');

});