<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Imports\MahasiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class MahasiswaController extends Controller
{
    /**
     * Helper private untuk mem-parse string tahun masuk.
     * Logika ini sama dengan di MahasiswaImport.
     */
    private function parseTahunMasuk(string $tahunMasukString): array
    {
        $semester_masuk_awal = null;
        if (str_contains(strtoupper($tahunMasukString), 'T.A. GANJIL')) {
            $semester_masuk_awal = 1;
        } elseif (str_contains(strtoupper($tahunMasukString), 'T.A. GENAP')) {
            $semester_masuk_awal = 2;
        }

        $tahun_masuk_awal = null;
        if (preg_match('/(\d{4})\/\d{4}/', $tahunMasukString, $matches)) {
            $tahun_masuk_awal = (int) $matches[1]; // Ekstrak "2022"
        }

        return [
            'tahun_masuk_awal' => $tahun_masuk_awal,
            'semester_masuk_awal' => $semester_masuk_awal,
        ];
    }

    // ... (fungsi index() Anda tidak berubah)
    public function index(Request $request)
    {
        $search = trim($request->input('search')); // Ambil input search

        // 1. Mulai query database
        $query = Mahasiswa::orderBy('tahun_masuk_awal', 'desc')
                              ->orderBy('semester_masuk_awal', 'desc');
        
        $isSemesterSearch = false;
        
        // Cek apakah input adalah angka semester (1-8)
        if (is_numeric($search) && $search > 0 && $search <= 8) {
            $isSemesterSearch = true;
        }

        // Terapkan filter standard database (jika BUKAN hanya pencarian semester)
        if (!$isSemesterSearch && $search) {
            // Gunakan where() dengan closure untuk mengelompokkan kondisi OR (WHERE (A OR B OR C...))
            $query->where(function ($q) use ($search) {
                
                // 1. Pencarian di NIM dan NAMA
                $q->where('nim', 'like', '%' . $search . '%')
                  ->orWhere('nama', 'like', '%' . $search . '%');
                  
                // 2. Logika Pencarian Tahun Masuk (kolom fisik)
                $lowerSearch = strtolower($search);
                
                // Cek apakah input adalah tahun (4 digit angka)
                if (preg_match('/^\d{4}$/', $search)) {
                    $q->orWhere('tahun_masuk_awal', (int) $search);
                }
                
                // Cek jika input mengandung kata 'ganjil' atau 'genap' (kolom fisik)
                if (str_contains($lowerSearch, 'ganjil')) {
                    $q->orWhere('semester_masuk_awal', 1);
                } elseif (str_contains($lowerSearch, 'genap')) {
                    $q->orWhere('semester_masuk_awal', 2);
                }
                
                // Cek jika input adalah format tahun masuk (misal: 2022/2023)
                if (preg_match('/(\d{4})/', $search, $matches)) {
                    $q->orWhere('tahun_masuk_awal', (int) $matches[1]);
                }
            });
        }
        
        // 2. Ambil data dari database
        $mahasiswa = $query->get();
        
        // 3. Filter Collection berdasarkan SEMESTER (Kolom Virtual)
        if ($isSemesterSearch) {
            $targetSemester = (int) $search;
            // Filter koleksi Mahasiswa menggunakan Accessor 'semester'
            $mahasiswa = $mahasiswa->filter(function ($m) use ($targetSemester) {
                // $m->semester memanggil Accessor
                return $m->semester == $targetSemester; 
            })->values(); // Reset keys after filtering
        }
        
        // Logika AJAX tetap sama
        if ($request->ajax()) {
            return view('mahasiswa._table_rows', ['mahasiswa' => $mahasiswa, 'search' => $search])->render();
        }

        // Jika request normal (non-AJAX)
        return view('mahasiswa.index', [
            'mahasiswa' => $mahasiswa,
            'search' => $search // Kirim variabel search
        ]);
    }

    // ... (fungsi create() Anda tidak berubah)
    public function create()
    {
        return view('mahasiswa.create');
    }

    /**
     * Simpan data mahasiswa baru ke database (CREATE - Action)
     * DIPERBARUI
     */
    public function store(Request $request)
    {
        // 1. Validasi data (sekarang menerima string)
        $validatedData = $request->validate([
            'nim' => ['required', 'string', 'max:20', 'unique:mahasiswa,nim'],
            'nama' => ['required', 'string', 'max:255'],
            'tahun_masuk_string' => [
                'required',
                'string',
                'regex:~T\.A\. (GANJIL|GENAP) \d{4}/\d{4}~i' // Sekarang aman di dalam array
            ],
            'no_telp' => ['nullable', 'string', 'max:20'],
        ]);
        
        // 2. Parse string tahun masuk
        $parsedData = $this->parseTahunMasuk($request->tahun_masuk_string);
        
        // 3. Gabungkan data
        $dataToCreate = array_merge(
            $validatedData,
            $parsedData,
            ['email' => $validatedData['nim'] . '@stikom-bali.ac.id']
        );

        // 4. Buat data baru
        Mahasiswa::create($dataToCreate);

        // 5. Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('mahasiswa.index')
                         ->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    // ... (fungsi show() Anda tidak berubah)
    public function show(Mahasiswa $mahasiswa)
    {
        return view('mahasiswa.show', ['mahasiswa' => $mahasiswa]);
    }

    // ... (fungsi edit() Anda tidak berubah)
    public function edit(Mahasiswa $mahasiswa)
    {
        return view('mahasiswa.edit', ['mahasiswa' => $mahasiswa]);
    }

    /**
     * Update data mahasiswa yang ada (UPDATE - Action)
     * DIPERBARUI
     */
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        // 1. Validasi data (menerima string)
        $validatedData = $request->validate([
            'nim' => [
                'required', 'string', 'max:20',
                Rule::unique('mahasiswa', 'nim')->ignore($mahasiswa->nim, 'nim')
            ],
            'nama' => ['required', 'string', 'max:255'],
            'tahun_masuk_string' => [
                'required',
                'string',
                'regex:~T\.A\. (GANJIL|GENAP) \d{4}/\d{4}~i' // Sekarang aman di dalam array
            ],
            'no_telp' => ['nullable', 'string', 'max:20'],
        ]);

        // 2. Parse string tahun masuk
        $parsedData = $this->parseTahunMasuk($request->tahun_masuk_string);
        
        // 3. Gabungkan data
        $dataToUpdate = array_merge(
            $validatedData,
            $parsedData,
            ['email' => $validatedData['nim'] . '@stikom-bali.ac.id']
        );

        // 4. Update data
        $mahasiswa->update($dataToUpdate);

        // 5. Redirect kembali ke halaman detail dengan pesan sukses
        return redirect()->route('mahasiswa.show', $mahasiswa->nim)
                         ->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    // ... (fungsi destroy() Anda tidak berubah)
    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->delete();
        return redirect()->route('mahasiswa.index')
                         ->with('success', 'Data mahasiswa berhasil dihapus.');
    }
    
    // ... (fungsi showImportForm() dan storeImport() Anda tidak berubah)
    public function showImportForm()
    {
        return view('mahasiswa.import');
    }

    public function storeImport(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        try {
            Excel::import(new MahasiswaImport, $request->file('file'));
            return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diimpor.');
        } catch (ValidationException $e) {
             $failure = $e->failures()[0];
             $errorMessage = "Error di baris " . $failure->row() . ": " . $failure->errors()[0];
             return redirect()->route('mahasiswa.importForm')->withErrors(['file' => $errorMessage]);
        } catch (\Exception $e) {
             return redirect()->route('mahasiswa.importForm')->withErrors(['file' => 'Terjadi error saat mengimpor file: ' . $e->getMessage()]);
        }
    }
}