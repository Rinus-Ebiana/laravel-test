<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\ScheduleEntry;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Impor PDF

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
        // 1. Ambil jadwal permanen terakhir (logika sama dengan index)
        $latestPermanentSchedule = Schedule::where('is_permanent', true)
                                           ->orderBy('created_at', 'desc')
                                           ->first();

        $entriesBySemester = collect();
        if ($latestPermanentSchedule) {
            $entries = $latestPermanentSchedule->entries()->with('matakuliah', 'dosen')->get();
            $entriesBySemester = $entries->sortBy('matakuliah.semester')
                                         ->groupBy(function($entry) {
                                             return $entry->matakuliah->semester;
                                         });
        }
        
        // 2. Tentukan nama file
        $fileName = 'jadwal_kelas_' . ($latestPermanentSchedule->nama_jadwal ?? 'terbaru') . '.pdf';

        // 3. Render view 'unduh.jadwal-kelas'
        $pdf = Pdf::loadView('unduh.jadwal-kelas', [
            'schedule' => $latestPermanentSchedule,
            'entriesBySemester' => $entriesBySemester
        ]);
        
        $pdf->setPaper('a4', 'landscape');

        // 5. Download file
        return $pdf->download($fileName);
    }
}