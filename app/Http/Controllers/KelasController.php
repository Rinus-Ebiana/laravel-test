<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\ScheduleEntry;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Impor PDF
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KelasExport;

class KelasController extends Controller
{
    /**
     * Menampilkan jadwal permanen terakhir untuk diisi data kelas.
     */
    public function index()
    {
        // 1. Ambil jadwal terakhir yang disimpan PERMANEN
        $latestPermanentSchedule = Schedule::where('is_permanent', true)
                                           ->orderBy('created_at', 'desc')
                                           ->first();

        // 2. Jika ada, ambil entrinya dan kelompokkan berdasarkan semester matakuliah
        $entriesBySemester = collect();
        if ($latestPermanentSchedule) {
            $entries = $latestPermanentSchedule->entries()
                                               ->with('matakuliah', 'dosen') // Eager load
                                               ->get();
            
            // Kelompokkan berdasarkan semester
            $entriesBySemester = $entries->sortBy('matakuliah.semester')
                                         ->groupBy(function($entry) {
                                             return $entry->matakuliah->semester;
                                         });
        }

        return view('kelas.index', [
            'schedule' => $latestPermanentSchedule,
            'entriesBySemester' => $entriesBySemester
        ]);
    }

    /**
     * Menerima data dari AJAX dan meng-update 'kode_kelas' dan 'ruang_kelas'.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'entries' => 'required|array',
            'entries.*.id' => 'required|integer|exists:schedule_entries,id',
            'entries.*.kelas' => 'nullable|string|max:100',
            'entries.*.ruang' => 'nullable|string|max:100',
        ]);

        try {
            // Gunakan transaksi untuk update massal
            DB::transaction(function () use ($validated) {
                foreach ($validated['entries'] as $data) {
                    ScheduleEntry::where('id', $data['id'])->update([
                        'kode_kelas' => $data['kelas'],
                        'ruang_kelas' => $data['ruang']
                    ]);
                }
            });
            
            return response()->json(['success' => true, 'message' => 'Data kelas berhasil disimpan.']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mengunduh jadwal kelas sebagai PDF.
     */
    public function download()
    {
        $latestPermanentSchedule = Schedule::where('is_permanent', true)
                                           ->orderBy('created_at', 'desc')
                                           ->first();

        if (!$latestPermanentSchedule) {
            return redirect()->route('kelas.index')->withErrors(['msg' => 'Tidak ada jadwal permanen.']);
        }

        // ... (logika pengambilan data entriesBySemester tetap sama) ...
        $entries = $latestPermanentSchedule->entries()->with('matakuliah', 'dosen')->get();
        $entriesBySemester = $entries->sortBy('matakuliah.semester')
                                     ->groupBy(function($entry) {
                                         return $entry->matakuliah->semester;
                                     });
        
        // [PERBAIKAN] Ganti '/' dengan '_'
        $safeName = str_replace(['/', '\\'], '_', $latestPermanentSchedule->nama_jadwal);
        $fileName = 'Jadwal_Kelas_' . $safeName . '.pdf';

        $pdf = Pdf::loadView('unduh.jadwal-kelas', [
            'schedule' => $latestPermanentSchedule,
            'entriesBySemester' => $entriesBySemester
        ]);
        
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download($fileName);
    }

    /**
     * Mengunduh jadwal kelas sebagai Excel.
     */
    public function downloadExcel()
    {
        $latestPermanentSchedule = Schedule::where('is_permanent', true)
                                           ->orderBy('created_at', 'desc')
                                           ->first();

        if (!$latestPermanentSchedule) {
            return redirect()->route('kelas.index')->withErrors(['msg' => 'Tidak ada jadwal permanen.']);
        }

        $entries = $latestPermanentSchedule->entries()->with('matakuliah', 'dosen')->get();
        $entriesBySemester = $entries->sortBy('matakuliah.semester')
                                     ->groupBy(function($entry) {
                                         return $entry->matakuliah->semester;
                                     });

        $safeName = str_replace(['/', '\\'], '_', $latestPermanentSchedule->nama_jadwal);
        $fileName = 'Jadwal_Kelas_' . $safeName . '.xlsx';

        return Excel::download(new KelasExport($latestPermanentSchedule, $entriesBySemester), $fileName);
    }
}
