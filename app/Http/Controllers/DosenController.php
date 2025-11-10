<?php

namespace App\Http\Controllers;

use App\Models\Dosen; // Pastikan Model Dosen di-impor
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Kita akan gunakan ini untuk validasi 'unique'

class DosenController extends Controller
{
    /**
     * Tampilkan semua data dosen (READ)
     */
    public function index()
    {
        // Ambil data dan urutkan berdasarkan KD
        $dosen = Dosen::orderBy('kd', 'asc')->get();
        return view('dosen.index', ['dosen' => $dosen]);
    }

    /**
     * Tampilkan form untuk menambah dosen baru (CREATE - Form)
     */
    public function create()
    {
        return view('dosen.create');
    }

    /**
     * Simpan data dosen baru ke database (CREATE - Action)
     */
    public function store(Request $request)
    {
        // 1. Validasi data
        $validatedData = $request->validate([
            'kd' => 'required|string|max:10|unique:dosen,kd',
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:50|unique:dosen,nip',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:dosen,email',
        ]);

        // 2. Buat data baru
        Dosen::create($validatedData);

        // 3. Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('dosen.index')
                         ->with('success', 'Data dosen berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail satu dosen (READ - Detail)
     */
    public function show(Dosen $dosen)
    {
        // Laravel akan otomatis menemukan dosen berdasarkan 'kd'
        
        // SEKARANG, kita muat relasi matakuliah-nya DENGAN URUTAN
        // Kita urutkan berdasarkan 'semester' secara ascending (naik)
        $dosen->load(['matakuliah' => function ($query) {
            $query->orderBy('semester', 'asc');
        }]);

        return view('dosen.show', ['dosen' => $dosen]);
    }

    /**
     * Tampilkan form untuk mengedit dosen (UPDATE - Form)
     */
    public function edit(Dosen $dosen)
    {
        // Laravel otomatis menemukan dosen, lalu kita kirim ke view
        return view('dosen.edit', ['dosen' => $dosen]);
    }

    /**
     * Update data dosen yang ada di database (UPDATE - Action)
     */
    public function update(Request $request, Dosen $dosen)
    {
        // 1. Validasi data
        $validatedData = $request->validate([
            'kd' => [
                'required',
                'string',
                'max:10',
                Rule::unique('dosen', 'kd')->ignore($dosen->kd, 'kd') // Abaikan kd saat ini
            ],
            'nama' => 'required|string|max:255',
            'nip' => [
                'required',
                'string',
                'max:50',
                Rule::unique('dosen', 'nip')->ignore($dosen->nip, 'nip') // Abaikan nip saat ini
            ],
            'no_telp' => 'nullable|string|max:20',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('dosen', 'email')->ignore($dosen->email, 'email') // Abaikan email saat ini
            ],
        ]);

        // 2. Update data
        $dosen->update($validatedData);

        // 3. Redirect kembali ke halaman detail dengan pesan sukses
        return redirect()->route('dosen.show', $dosen->kd)
                         ->with('success', 'Data dosen berhasil diperbarui.');
    }

    /**
     * Hapus data dosen dari database (DELETE - Action)
     */
    public function destroy(Dosen $dosen)
    {
        // Hapus data
        $dosen->delete();

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('dosen.index')
                         ->with('success', 'Data dosen berhasil dihapus.');
    }
}