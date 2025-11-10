<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Matakuliah;
use App\Models\Schedule;
use App\Models\KrsRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class KrsController extends Controller
{
    /**
     * Halaman index KRS.
     * Menampilkan "folder" angkatan (tahun masuk + semester masuk).
     */
    public function index()
    {
        // 1. Ambil semua kombinasi tahun & semester yang unik
        $angkatan = Mahasiswa::select('tahun_masuk_awal', 'semester_masuk_awal')
                             ->distinct()
                             ->whereNotNull('tahun_masuk_awal')
                             ->orderBy('tahun_masuk_awal', 'desc')
                             ->orderBy('semester_masuk_awal', 'desc')
                             ->get(); // -> [{2024, 1}, {2023, 2}, {2023, 1}, {2022, 2}]
        
        // 2. Ubah data menjadi format yang kita inginkan
        $formattedAngkatan = $angkatan->map(function($item) {
            $tahun = $item->tahun_masuk_awal;
            $semester = $item->semester_masuk_awal;
            
            $semesterString = ($semester == 1) ? "GANJIL" : "GENAP";
            $tahunString = $tahun . '/' . ($tahun + 1);
            
            return (object) [
                'nama_folder' => "TAHUN AJARAN $semesterString $tahunString",
                'slug' => $tahun . '-' . $semester // Slug unik: "2023-1", "2023-2"
            ];
        });

        return view('krs.index', ['angkatan' => $formattedAngkatan]);
    }

    /**
     * Menampilkan daftar mahasiswa per angkatan + semester.
     */
    public function showAngkatan($slug)
    {
        // 1. Pecah slug "2023-1" kembali menjadi tahun dan semester
        $parts = explode('-', $slug);
        $tahun_awal = $parts[0] ?? null;
        $semester_awal = $parts[1] ?? null;

        if (!$tahun_awal || !$semester_awal) {
            abort(404, 'Angkatan tidak valid.');
        }

        // 2. Cari mahasiswa yang cocok
        $mahasiswa = Mahasiswa::where('tahun_masuk_awal', $tahun_awal)
                              ->where('semester_masuk_awal', $semester_awal)
                              ->orderBy('nama', 'asc')
                              ->get();
        
        // 3. Buat ulang nama folder untuk judul
        $semesterString = ($semester_awal == 1) ? "GANJIL" : "GENAP";
        $tahunString = $tahun_awal . '/' . ($tahun_awal + 1);
        $nama_angkatan = "TAHUN AJARAN $semesterString $tahunString";

        return view('krs.show_angkatan', [
            'angkatan' => $nama_angkatan,
            'slug' => $slug, // Kirim slug untuk tombol "Kembali"
            'mahasiswa' => $mahasiswa
        ]);
    }

    // ... (fungsi editNilai tidak berubah) ...
    public function editNilai(Mahasiswa $mahasiswa)
    {
        $matakuliah = Matakuliah::orderBy('semester', 'asc')->get();
        $nilaiSudahAda = $mahasiswa->nilaiMatakuliah->pluck('pivot.nilai', 'kode_mk');
        $semesterMahasiswa = $mahasiswa->semester;

        return view('krs.edit_nilai', [
            'mahasiswa' => $mahasiswa,
            'matakuliah' => $matakuliah,
            'nilaiSudahAda' => $nilaiSudahAda,
            'semesterMahasiswa' => $semesterMahasiswa
        ]);
    }

    // ... (fungsi storeNilai tidak berubah) ...
    public function storeNilai(Request $request, Mahasiswa $mahasiswa)
    {
        $validated = $request->validate([ 'nilai' => 'nullable|array' ]);
        
        $syncData = [];
        if (!empty($validated['nilai'])) {
            foreach ($validated['nilai'] as $kode_mk => $nilai) {
                if (!empty($nilai)) {
                    $syncData[$kode_mk] = ['nilai' => $nilai];
                }
            }
        }
        
        $mahasiswa->nilaiMatakuliah()->sync($syncData);

        // PERBAIKAN: Arahkan kembali ke halaman "Edit Nilai"
        return redirect()->route('krs.editNilai', $mahasiswa->nim)
                         ->with('success', 'Nilai berhasil disimpan.');
    }
    
    /**
     * Menampilkan halaman "Susun KRS" (pilih kelas) untuk 1 angkatan.
     */
    public function susunAngkatan($slug)
    {
        // 1. Ambil data angkatan (mahasiswa)
        $parts = explode('-', $slug);
        $tahun_awal = $parts[0] ?? null;
        $semester_awal = $parts[1] ?? null;
        $students = Mahasiswa::where('tahun_masuk_awal', $tahun_awal)
                             ->where('semester_masuk_awal', $semester_awal)
                             ->orderBy('nama', 'asc')
                             ->get();
        
        if ($students->isEmpty()) {
            return redirect()->route('krs.index')->withErrors(['msg' => 'Tidak ada mahasiswa di angkatan ini.']);
        }

        // 2. Ambil jadwal kelas permanen yang aktif
        $schedule = Schedule::where('is_permanent', true)
                            ->orderBy('created_at', 'desc')
                            ->first();

        if (!$schedule) {
            return back()->withErrors(['msg' => 'Admin belum mengatur Jadwal Kelas permanen.']);
        }

        // 3. Ambil SEMUA data yang diperlukan
        
        // a. Daftar slot (kolom): Senin 18:00, Selasa 18:00...
        $slots = [
            'Senin 18:00–20:30', 'Selasa 18:00–20:30', 'Rabu 18:00–20:30', 
            'Kamis 18:00–20:30', 'Jumat 18:00–20:30', 'Sabtu 13:00–15:30', 
            'Sabtu 15:30–18:00', 'Sabtu 18:00–20:30'
        ];
        
        // b. Kelas yang ditawarkan, di-grup berdasarkan slot
        $classes_by_slot = $schedule->entries()
                                    ->with('matakuliah', 'dosen')
                                    ->get()
                                    ->groupBy(function($entry) {
                                        return $entry->hari . ' ' . $entry->jam_slot;
                                    });

        // c. Matakuliah tanpa slot (Publikasi, Seminar, Tesis)
        $kode_mk_akhir = ['M1241111', 'M1241113', 'M1241109']; // Publikasi, Seminar, Tesis
        $mk_tanpa_slot = Matakuliah::whereIn('kode_mk', $kode_mk_akhir) // <-- INI QUERY YANG BENAR
                                   ->orderBy('semester', 'asc')
                                   ->get();
        
        // d. Ambil semua NIM mahasiswa di angkatan ini
        $student_nims = $students->pluck('nim');

        // e. Ambil SEMUA nilai untuk SEMUA mahasiswa di angkatan ini (Efisien)
        $grades_map = DB::table('mahasiswa_matakuliah')
                        ->whereIn('mahasiswa_nim', $student_nims)
                        ->get()
                        ->groupBy('mahasiswa_nim')
                        ->map(fn($item) => $item->pluck('nilai', 'matakuliah_kode_mk'));
        
        // f. Ambil KRS yang sudah disimpan sebelumnya (Efisien)
        $krs_map = KrsRecord::whereIn('mahasiswa_nim', $student_nims)
                            ->get()
                            ->groupBy('mahasiswa_nim');

        // *** PERBAIKAN: Ambil daftar MK prasyarat untuk Tesis ***
        // (Semua MK kecuali Tesis itu sendiri, yang di Sem 4)
        $mk_pra_tesis = Matakuliah::where('semester', '<', 4)->pluck('kode_mk');

        return view('krs.susun_angkatan', [
            'slug' => $slug,
            'students' => $students,
            'slots' => $slots,
            'classes_by_slot' => $classes_by_slot,
            'mk_tanpa_slot' => $mk_tanpa_slot,
            'grades_map' => $grades_map,
            'krs_map' => $krs_map,
            'mk_pra_tesis' => $mk_pra_tesis // <-- Kirim data prasyarat ke view
        ]);
    }

    /**
     * Menyimpan KRS yang sudah disusun untuk 1 angkatan.
     */
    public function storeAngkatan(Request $request, $slug)
    {
        // Data yang datang:
        // $request->krs[nim][slot_key] = schedule_entry_id
        // $request->krs_mk[nim][] = matakuliah_kode_mk
        
        $krs_data = $request->input('krs', []);
        $krs_mk_data = $request->input('krs_mk', []);
        
        $parts = explode('-', $slug);
        $tahun_awal = $parts[0] ?? null;
        $semester_awal = $parts[1] ?? null;
        $student_nims = Mahasiswa::where('tahun_masuk_awal', $tahun_awal)
                                 ->where('semester_masuk_awal', $semester_awal)
                                 ->pluck('nim');

        DB::transaction(function () use ($student_nims, $krs_data, $krs_mk_data) {
            
            // 1. Hapus SEMUA krs lama untuk SELURUH angkatan ini
            KrsRecord::whereIn('mahasiswa_nim', $student_nims)->delete();

            $recordsToInsert = [];

            // 2. Proses data KRS (yang ada jadwalnya)
            foreach ($krs_data as $nim => $slots) {
                if (!$student_nims->contains($nim)) continue; // Keamanan
                
                foreach ($slots as $schedule_entry_id) {
                    if (!empty($schedule_entry_id)) {
                        $recordsToInsert[] = [
                            'mahasiswa_nim' => $nim,
                            'schedule_entry_id' => $schedule_entry_id,
                            'matakuliah_kode_mk' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            // 3. Proses data MK (Tesis, dll)
            foreach ($krs_mk_data as $nim => $matakuliahs) {
                if (!$student_nims->contains($nim)) continue; // Keamanan
                
                foreach ($matakuliahs as $kode_mk) {
                    if (!empty($kode_mk)) {
                        $recordsToInsert[] = [
                            'mahasiswa_nim' => $nim,
                            'schedule_entry_id' => null,
                            'matakuliah_kode_mk' => $kode_mk,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
            
            // 4. Masukkan semua data baru dalam 1 query (Sangat Cepat)
            KrsRecord::insert($recordsToInsert);
        });
        
        return redirect()->route('krs.showAngkatan', $slug)
                         ->with('success', 'KRS untuk angkatan ini berhasil disimpan.');
    }

    /**
     * FUNGSI BARU UNTUK MENGUNDUH KRS ANGKATAN
     */
    public function downloadAngkatan($slug)
    {
        // 1. Ambil data angkatan (mahasiswa)
        $parts = explode('-', $slug);
        $tahun_awal = $parts[0] ?? null;
        $semester_awal = $parts[1] ?? null;
        
        $students = Mahasiswa::where('tahun_masuk_awal', $tahun_awal)
                             ->where('semester_masuk_awal', $semester_awal)
                             ->orderBy('nama', 'asc')
                             ->get();
        
        if ($students->isEmpty()) {
            return redirect()->route('krs.index')->withErrors(['msg' => 'Tidak ada mahasiswa di angkatan ini.']);
        }
        
        // 2. Ambil data KRS yang sudah disimpan untuk semua mahasiswa ini
        $student_nims = $students->pluck('nim');
        
        $krs_records = KrsRecord::whereIn('mahasiswa_nim', $student_nims)
                                ->with(
                                    'scheduleEntry.matakuliah', // Relasi bertingkat
                                    'scheduleEntry.dosen',      // Relasi bertingkat
                                    'matakuliah' // Untuk Tesis, dll.
                                ) 
                                ->get();
        
        // 3. Kelompokkan KRS berdasarkan mahasiswa
        $krs_map = $krs_records->groupBy('mahasiswa_nim');

        // 4. Buat nama file
        $semesterString = ($semester_awal == 1) ? "GANJIL" : "GENAP";
        $fileName = 'KRS_Angkatan_' . $tahun_awal . '_' . $semesterString . '.pdf';

        // 5. Render view-nya menjadi HTML
        $pdf = Pdf::loadView('unduh.krs-angkatan', [
            'students' => $students,
            'krs_map' => $krs_map,
            'nama_angkatan' => "T.A. $semesterString $tahun_awal/" . ($tahun_awal + 1)
        ]);
        
        // 6. Download file (bukan landscape agar muat per halaman)
        return $pdf->download($fileName);
    }

}