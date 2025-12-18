<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Matakuliah;
use App\Models\Schedule;
use App\Models\ScheduleEntry;
use App\Models\KrsRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KrsAngkatanExport;
use App\Exports\TemplateNilaiExport;
use App\Imports\NilaiMahasiswaImport;

class KrsController extends Controller
{
    /**
     * Helper private untuk mem-parse slug angkatan menjadi tahun dan semester.
     */
    private function parseAngkatanSlug(string $slug): ?array
    {
        // Mencari 4 digit tahun
        if (preg_match('/(\d{4})/', $slug, $matches)) {
            $tahun_awal = (int) $matches[1];
        } else {
            return null;
        }

        $lowerSlug = strtolower($slug);
        $semester_awal = null;

        // Identifikasi semester dari kata kunci Ganjil/Genap atau angka 1/2
        if (str_contains($lowerSlug, 'ganjil') || str_contains($lowerSlug, '1')) {
            $semester_awal = 1;
        } elseif (str_contains($lowerSlug, 'genap') || str_contains($lowerSlug, '2')) {
            $semester_awal = 2;
        }

        if (!$tahun_awal || !$semester_awal) {
            return null;
        }
        
        return [
            'tahun_awal' => $tahun_awal,
            'semester_awal' => $semester_awal,
        ];
    }

    /**
     * Halaman index KRS.
     */
    public function index()
    {
        // ... (fungsi index tetap sama) ...
        $angkatan = Mahasiswa::select('tahun_masuk_awal', 'semester_masuk_awal')
                             ->distinct()
                             ->whereNotNull('tahun_masuk_awal')
                             ->orderBy('tahun_masuk_awal', 'desc')
                             ->orderBy('semester_masuk_awal', 'desc')
                             ->get();

        $formattedAngkatan = $angkatan->map(function($item) {
            $tahun = $item->tahun_masuk_awal;
            $semester = $item->semester_masuk_awal;
            $semesterString = ($semester == 1) ? "GANJIL" : "GENAP";
            $tahunString = $tahun . '/' . ($tahun + 1);
            $slug = strtolower(str_replace(['T.A. ', ' ', '/'], ['', '-', '-'], "T.A. $semesterString $tahunString"));

            // Check if all students in this angkatan have KRS
            $totalStudents = Mahasiswa::where('tahun_masuk_awal', $tahun)
                                      ->where('semester_masuk_awal', $semester)
                                      ->count();

            $studentsWithKrs = Mahasiswa::where('tahun_masuk_awal', $tahun)
                                        ->where('semester_masuk_awal', $semester)
                                        ->whereHas('krsRecords')
                                        ->count();

            $allHaveKrs = $totalStudents > 0 && $totalStudents === $studentsWithKrs;

            return (object) [
                'nama_folder' => "Angkatan T.A. $semesterString $tahunString",
                'tahun' => $tahun,
                'semester' => $semester,
                'slug' => $slug,
                'all_have_krs' => $allHaveKrs
            ];
        });

        return view('krs.index', ['angkatan' => $formattedAngkatan]);
    }

    /**
     * Menampilkan daftar mahasiswa per angkatan + semester.
     */
    public function showAngkatan(Request $request, $slug)
    {
        // ... (fungsi showAngkatan tetap sama) ...
        $search = trim($request->input('search'));
        $parsed = $this->parseAngkatanSlug($slug);

        if (!$parsed) {
            return redirect()->route('krs.index')->withErrors(['msg' => 'Format angkatan tidak valid.']);
        }
        
        $tahun_awal = $parsed['tahun_awal'];
        $semester_awal = $parsed['semester_awal'];
        
        $mahasiswaQuery = Mahasiswa::where('tahun_masuk_awal', $tahun_awal)
                                   ->where('semester_masuk_awal', $semester_awal)
                                   ->orderBy('nama', 'asc');

        if ($search) {
            $mahasiswaQuery->where(function ($q) use ($search) {
                $q->where('nim', 'like', '%' . $search . '%')
                  ->orWhere('nama', 'like', '%' . $search . '%');
            });
        }
        
        $mahasiswa = $mahasiswaQuery->get();
        $semesterString = ($semester_awal == 1) ? "GANJIL" : "GENAP";
        $namaAngkatan = "T.A. $semesterString $tahun_awal/" . ($tahun_awal + 1);

        if ($request->ajax()) {
            return view('krs._show_angkatan_table_rows', [
                'mahasiswa' => $mahasiswa,
                'slug' => $slug 
            ])->render();
        }
        
        return view('krs.show_angkatan', [
            'mahasiswa' => $mahasiswa,
            'angkatan' => $namaAngkatan,
            'slug' => $slug,
            'search' => $search 
        ]);
    }

    // ... (fungsi editNilai, storeNilai, susunAngkatan, dan storeAngkatan tetap sama) ...
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

        return redirect()->route('krs.editNilai', $mahasiswa->nim)
                         ->with('success', 'Nilai berhasil disimpan.');
    }
    
    /**
     * Menampilkan halaman "Susun KRS" (pilih kelas) untuk 1 angkatan.
     */
    public function susunAngkatan($slug)
    {
        $parsed = $this->parseAngkatanSlug($slug);

        if (!$parsed) {
            return redirect()->route('krs.index')->withErrors(['msg' => 'Format angkatan tidak valid untuk penyusunan KRS.']);
        }
        
        $tahun_awal = $parsed['tahun_awal'];
        $semester_awal = $parsed['semester_awal'];

        $students = Mahasiswa::where('tahun_masuk_awal', $tahun_awal)
                             ->where('semester_masuk_awal', $semester_awal)
                             ->orderBy('nama', 'asc')
                             ->get();
        
        if ($students->isEmpty()) {
            return redirect()->route('krs.index')->withErrors(['msg' => 'Tidak ada mahasiswa di angkatan ini.']);
        }

        $schedule = Schedule::where('is_permanent', true)
                            ->orderBy('created_at', 'desc')
                            ->first();

        if (!$schedule) {
            return back()->withErrors(['msg' => 'Admin belum mengatur Jadwal Kelas permanen.']);
        }

        $slots = [
            'Senin 18:00–20:30', 'Selasa 18:00–20:30', 'Rabu 18:00–20:30', 
            'Kamis 18:00–20:30', 'Jumat 18:00–20:30', 'Sabtu 13:00–15:30', 
            'Sabtu 15:30–18:00', 'Sabtu 18:00–20:30'
        ];
        
        $classes_by_slot = $schedule->entries()
                                    ->with('matakuliah', 'dosen')
                                    ->get()
                                    ->groupBy(function($entry) {
                                        return $entry->hari . ' ' . $entry->jam_slot;
                                    });

        $kode_mk_akhir = ['M1241111', 'M1241113', 'M1241109'];
        $mk_tanpa_slot = Matakuliah::whereIn('kode_mk', $kode_mk_akhir)
                                   ->orderBy('semester', 'asc')
                                   ->get();
        
        $student_nims = $students->pluck('nim');

        $grades_map = DB::table('mahasiswa_matakuliah')
                        ->whereIn('mahasiswa_nim', $student_nims)
                        ->get()
                        ->groupBy('mahasiswa_nim')
                        ->map(fn($item) => $item->pluck('nilai', 'matakuliah_kode_mk'));
        
        $krs_map = KrsRecord::whereIn('mahasiswa_nim', $student_nims)
                            ->get()
                            ->groupBy('mahasiswa_nim');

        $mk_pra_tesis = Matakuliah::where('semester', '<', 4)->pluck('kode_mk');

        $semesterString = ($semester_awal == 1) ? "GANJIL" : "GENAP";
        $angkatan_nama = "T.A. $semesterString $tahun_awal/" . ($tahun_awal + 1);

        return view('krs.susun_angkatan', [
            'slug' => $slug,
            'mahasiswa' => $students,
            'angkatan' => $angkatan_nama,
            'search' => '',
            'classes_by_slot' => $classes_by_slot,
            'krs_map' => $krs_map,
            'mk_tanpa_slot' => $mk_tanpa_slot,
            'grades_map' => $grades_map,
            'mk_pra_tesis' => $mk_pra_tesis,
            'slots' => $slots
        ]);
    }

    /**
     * Menyimpan KRS yang sudah disusun untuk 1 angkatan.
     */
    public function storeAngkatan(Request $request, $slug)
    {
        $krs_data = $request->input('krs', []);
        $krs_mk_data = $request->input('krs_mk', []);

        $parsed = $this->parseAngkatanSlug($slug);

        if (!$parsed) {
            return redirect()->back()->withErrors(['msg' => 'Format angkatan tidak valid untuk penyimpanan KRS.']);
        }

        $tahun_awal = $parsed['tahun_awal'];
        $semester_awal = $parsed['semester_awal'];
        $student_nims = Mahasiswa::where('tahun_masuk_awal', $tahun_awal)
                                 ->where('semester_masuk_awal', $semester_awal)
                                 ->pluck('nim');

        DB::transaction(function () use ($student_nims, $krs_data, $krs_mk_data) {
            
            KrsRecord::whereIn('mahasiswa_nim', $student_nims)->delete();
            $recordsToInsert = [];

            foreach ($krs_data as $nim => $slots) {
                if (!$student_nims->contains($nim)) continue;
                
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

            foreach ($krs_mk_data as $nim => $matakuliahs) {
                if (!$student_nims->contains($nim)) continue;
                
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
            
            KrsRecord::insert($recordsToInsert);
        });

        return redirect()->route('krs.showAngkatan', $slug)
                         ->with('success', 'KRS untuk angkatan ini berhasil disimpan.');
    }

    /**
     * FUNGSI UNTUK MENGUNDUH KRS ANGKATAN (PDF) - FUNGSI UTAMA
     */
    public function downloadAngkatan(string $slug)
    {
        ini_set('memory_limit', '512M'); 
        set_time_limit(300); 

        try {
            $parsed = $this->parseAngkatanSlug($slug);
            
            if (!$parsed) {
                return redirect()->route('krs.index')->withErrors(['msg' => 'Format angkatan tidak valid.']);
            }
            $tahun_awal = $parsed['tahun_awal'];
            $semester_awal = $parsed['semester_awal'];

            $students = Mahasiswa::where('tahun_masuk_awal', $tahun_awal)
                                 ->where('semester_masuk_awal', $semester_awal)
                                 ->orderBy('nama', 'asc')
                                 ->get();
            
            if ($students->isEmpty()) {
                return redirect()->route('krs.index')->withErrors(['msg' => 'Tidak ada mahasiswa di angkatan ini.']);
            }
            
            $student_nims = $students->pluck('nim');
            $krs_records = KrsRecord::whereIn('mahasiswa_nim', $student_nims)
                                    ->with('scheduleEntry.matakuliah', 'scheduleEntry.dosen', 'matakuliah')
                                    ->get();
            $krs_map = $krs_records->groupBy('mahasiswa_nim');
            $semesterString = ($semester_awal == 1) ? "GANJIL" : "GENAP";
            $fileName = 'KRS_Angkatan_' . $tahun_awal . '_' . $semesterString . '.pdf';
            
            $pdfData = [
                'students' => $students,
                'krs_map' => $krs_map,
                'nama_angkatan' => "T.A. $semesterString $tahun_awal/" . ($tahun_awal + 1)
            ];

            // Render view, menggunakan kode view Anda yang asli.
            $html = view('unduh.krs-angkatan', $pdfData)->render();
            
            $pdf = Pdf::loadHTML($html);
            return $pdf->download($fileName); 

        } catch (\Exception $e) {
            // Ini akan menangkap error (bukan silent crash) jika ada bug view
            return redirect()->back()->with('error', 'Gagal mengunduh PDF: ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Baris: ' . $e->getLine());
        }
    }

    public function downloadAngkatanExcel(string $slug)
    {
        ini_set('memory_limit', '512M'); 
        set_time_limit(300);

        $parsed = $this->parseAngkatanSlug($slug);

        if (!$parsed) {
            return redirect()->back()->withErrors(['msg' => 'Format angkatan tidak valid.']);
        }

        $tahun_awal = $parsed['tahun_awal'];
        $semester_awal = $parsed['semester_awal'];

        $students = Mahasiswa::where('tahun_masuk_awal', $tahun_awal)
            ->where('semester_masuk_awal', $semester_awal)
            ->orderBy('nama', 'asc')
            ->get();

        if ($students->isEmpty()) {
            return redirect()->back()->withErrors(['msg' => 'Tidak ada mahasiswa di angkatan ini.']);
        }

        $student_nims = $students->pluck('nim');

        $krs_records = KrsRecord::whereIn('mahasiswa_nim', $student_nims)
            ->with('scheduleEntry.matakuliah', 'scheduleEntry.dosen', 'matakuliah')
            ->get();

        $krs_map = $krs_records->groupBy('mahasiswa_nim');

        $semesterString = ($semester_awal == 1) ? "GANJIL" : "GENAP";
        $nama_angkatan = "T.A. $semesterString $tahun_awal/" . ($tahun_awal + 1);

        return Excel::download(
            new KrsAngkatanExport($students, $krs_map, $nama_angkatan),
            'KRS_Angkatan_' . $tahun_awal . '_' . $semesterString . '.xlsx'
        );
    }

    public function importNilai(Request $request, Mahasiswa $mahasiswa)
    {
        \Log::info('ImportNilai method called for mahasiswa: ' . $mahasiswa->nim);

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // Max 10MB
        ]);

        \Log::info('File validation passed. File: ' . $request->file('excel_file')->getClientOriginalName());

        try {
            \Log::info('Starting Excel import...');
            Excel::import(new NilaiMahasiswaImport($mahasiswa), $request->file('excel_file'));
            \Log::info('Excel import completed successfully');

            return redirect()->back()->with('success', 'Nilai berhasil diimpor dari Excel.');
        } catch (\Exception $e) {
            \Log::error('Import failed with exception: ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
            return redirect()->back()->withErrors(['msg' => 'Gagal mengimpor nilai: ' . $e->getMessage()]);
        }
    }

    public function downloadTemplateNilai()
    {
        try {
            return Excel::download(new TemplateNilaiExport(), 'template_nilai.xlsx');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
