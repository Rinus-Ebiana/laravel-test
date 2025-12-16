<?php

namespace App\Http\Controllers;

use App\Models\Matakuliah;
use App\Models\Dosen; // <-- Impor Dosen
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MatakuliahController extends Controller
{
    /**
     * Tampilkan semua data (READ)
     */
    public function index(Request $request) // Tambahkan Request
    {
        $search = trim($request->input('search'));

        // Eager load relasi 'dosen' untuk efisiensi
        $query = Matakuliah::with('dosen')->orderBy('semester', 'asc');
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                // 1. Pencarian di Kode MK dan Nama MK
                $q->where('kode_mk', 'like', '%' . $search . '%')
                  ->orWhere('nama_mk', 'like', '%' . $search . '%');
                
                // 2. Pencarian di SKS dan Semester (jika input adalah angka)
                if (is_numeric($search) && $search > 0) {
                    // Cari di kolom 'sks' atau 'semester' jika inputnya angka
                    $q->orWhere('sks', (int) $search)
                      ->orWhere('semester', (int) $search);
                }

                // 3. Pencarian di Nama Dosen Pengampu (Relational Search)
                // Filter Matakuliah yang memiliki Dosen dengan nama yang cocok
                $q->orWhereHas('dosen', function ($dosenQuery) use ($search) {
                    $dosenQuery->where('nama', 'like', '%' . $search . '%');
                });
            });
        }
        
        $matakuliah = $query->get();
        
        // --- LOGIKA AJAX ---
        if ($request->ajax()) {
            // Mengembalikan view parsial untuk tbody
            return view('matakuliah._table_rows', ['matakuliah' => $matakuliah, 'search' => $search])->render();
        }

        // Jika request normal (non-AJAX)
        return view('matakuliah.index', ['matakuliah' => $matakuliah, 'search' => $search]);
    }

    /**
     * Simpan data baru (CREATE - Action)
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $validatedData = $request->validate([
            'kode_mk' => 'required|string|max:20|unique:matakuliah,kode_mk',
            'nama_mk' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:10',
            'semester' => 'required|integer|min:1|max:8',
            'dosen_kd' => 'nullable|array', // Pastikan ini array (dari select multiple)
            'dosen_kd.*' => 'string|exists:dosen,kd' // Pastikan setiap isinya ada di tabel dosen
        ]);

        // 2. Buat Matakuliah (tanpa dosen)
        $matakuliah = Matakuliah::create($validatedData);

        // 3. Lampirkan (Attach) Dosen jika ada
        if ($request->has('dosen_kd')) {
            $matakuliah->dosen()->attach($request->dosen_kd);
        }

        // 4. Redirect
        return redirect()->route('matakuliah.index')
                         ->with('success', 'Data matakuliah berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail (READ - Detail)
     */
    public function show(Matakuliah $matakuliah)
    {
        // $matakuliah sudah di-load, kita juga bisa load mahasiswanya
        // $matakuliah->load('mahasiswa'); // (Ini untuk nanti)
        return view('matakuliah.show', ['matakuliah' => $matakuliah]);
    }

    /**
     * Tampilkan form edit (UPDATE - Form)
     */
    public function edit(Matakuliah $matakuliah)
    {
        // Ambil semua dosen untuk daftar <select>
        $dosens = Dosen::orderBy('nama', 'asc')->get();
        
        // Ambil daftar KD dosen yang sudah terhubung ke MK ini
        $selectedDosens = $matakuliah->dosen->pluck('kd')->toArray();

        return view('matakuliah.edit', [
            'matakuliah' => $matakuliah,
            'dosens' => $dosens,
            'selectedDosens' => $selectedDosens // Kirim data ini ke view
        ]);
    }

    /**
     * Update data (UPDATE - Action)
     */
    public function update(Request $request, Matakuliah $matakuliah)
    {
        // 1. Validasi (perhatikan: 'nama' dan 'dosens')
        $validatedData = $request->validate([
            // 'kode_mk' tidak perlu divalidasi karena readonly
            'nama_mk' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:10',
            'semester' => 'required|integer|min:1|max:8',
            'dosens' => 'nullable|array', // Menerima 'dosens' (plural)
            'dosens.*' => 'string|exists:dosen,kd' // Pastikan setiap isinya ada
        ]);

        // 2. Update data dasar Matakuliah
        $matakuliah->update([
            'nama_mk' => $validatedData['nama_mk'],
            'sks' => $validatedData['sks'],
            'semester' => $validatedData['semester'],
        ]);

        // 3. Sinkronkan (Sync) Dosen
        // Sync = Hapus semua relasi lama, ganti dengan yang baru dari form
        $matakuliah->dosen()->sync($request->dosens ?? []); // Kirim array kosong jika tidak ada

        // 4. Redirect
        return redirect()->route('matakuliah.show', $matakuliah->kode_mk)
                         ->with('success', 'Data matakuliah berhasil diperbarui.');
    }

    /**
     * Hapus data (DELETE - Action)
     */
    public function destroy(Matakuliah $matakuliah)
    {
        // 1. Hapus semua relasi di tabel pivot
        $matakuliah->dosen()->detach();
        
        // 2. Hapus matakuliah
        $matakuliah->delete();

        return redirect()->route('matakuliah.index')
                         ->with('success', 'Data matakuliah berhasil dihapus.');
    }
}