<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Matakuliah;
use App\Models\Schedule;
use App\Models\ScheduleEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalController extends Controller
{
    /**
     * Halaman 'jadwal.index'
     * Menampilkan jadwal TERAKHIR yang 'is_active'.
     */
    public function index()
    {
        // Cari jadwal terakhir yang aktif (baik sementara atau permanen)
        $activeSchedule = Schedule::where('is_active', true)
                                  ->with('entries.matakuliah', 'entries.dosen') // Eager load
                                  ->orderBy('updated_at', 'desc')
                                  ->first();
                                  
        return view('jadwal.index', ['schedule' => $activeSchedule]);
    }

    /**
     * Halaman 'jadwal.history'
     * Menampilkan SEMUA jadwal yang 'is_permanent'.
     */
    public function history()
    {
        $permanentSchedules = Schedule::where('is_permanent', true)
                                      ->orderBy('created_at', 'desc')
                                      ->get();
                                      
        return view('jadwal.history', ['schedules' => $permanentSchedules]);
    }

    /**
     * Halaman 'jadwal.create' (susun_jadwal.html)
     * Menampilkan grid untuk menyusun jadwal baru.
     */
    public function create()
    {
        $matakuliah = Matakuliah::with('dosen')->orderBy('semester', 'asc')->get();
        
        return view('jadwal.create', ['matakuliah' => $matakuliah]);
    }

    /**
     * Menyimpan jadwal BARU (dari 'create')
     * Ini dipanggil oleh AJAX dari JavaScript.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jadwal' => 'required|string|max:255',
            'is_permanent' => 'required|boolean',
            'entries' => 'required|array',
            'entries.*.mk' => 'required|string|exists:matakuliah,kode_mk',
            'entries.*.dosen' => 'required|string|exists:dosen,kd',
            'entries.*.hari' => 'required|string',
            'entries.*.jam' => 'required|string',
        ]);
        
        // Gunakan Transaksi Database untuk memastikan data konsisten
        DB::transaction(function () use ($validated) {
            
            // 1. Jika ini 'Simpan Sementara', nonaktifkan draf lama
            if ($validated['is_permanent'] == false) {
                 Schedule::where('is_active', true)->where('is_permanent', false)
                         ->update(['is_active' => false]);
            }
            // Jika 'Simpan Permanen', nonaktifkan semua jadwal aktif sebelumnya
            else {
                 Schedule::where('is_active', true)->update(['is_active' => false]);
            }

            // 2. Buat Jadwal Induk (Schedule)
            $schedule = Schedule::create([
                'nama_jadwal' => $validated['nama_jadwal'],
                'is_permanent' => $validated['is_permanent'],
                'is_active' => true, // Selalu aktif saat dibuat
            ]);

            // 3. Loop dan buat semua Entri Detail
            foreach ($validated['entries'] as $entry) {
                ScheduleEntry::create([
                    'schedule_id' => $schedule->id,
                    'matakuliah_kode_mk' => $entry['mk'],
                    'dosen_kd' => $entry['dosen'],
                    'hari' => $entry['hari'],
                    'jam_slot' => $entry['jam'],
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Jadwal berhasil disimpan!']);
    }

    /**
     * Halaman 'jadwal.edit' (edit_jadwal.html)
     * Menampilkan grid yang sudah terisi data.
     */
    public function edit(Schedule $schedule)
    {
        // 1. Ambil jadwal yang mau diedit, beserta entrinya
        $schedule->load('entries');

        // 2. Ambil semua matakuliah (untuk me-render grid)
        $matakuliah = Matakuliah::with('dosen')->orderBy('semester', 'asc')->get();

        return view('jadwal.edit', [
            'schedule' => $schedule,
            'matakuliah' => $matakuliah
        ]);
    }

    /**
     * Menyimpan jadwal yang DIEDIT (dari 'edit')
     * Ini juga dipanggil oleh AJAX.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'nama_jadwal' => 'required|string|max:255',
            'is_permanent' => 'required|boolean',
            'entries' => 'required|array',
            'entries.*.mk' => 'required|string|exists:matakuliah,kode_mk',
            'entries.*.dosen' => 'required|string|exists:dosen,kd',
            'entries.*.hari' => 'required|string',
            'entries.*.jam' => 'required|string',
        ]);

        DB::transaction(function () use ($validated, $schedule) {
            
            // 1. Jika ini 'Simpan Sementara', nonaktifkan draf lama
            if ($validated['is_permanent'] == false) {
                 Schedule::where('is_active', true)->where('is_permanent', false)
                         ->where('id', '!=', $schedule->id) // kecuali dirinya sendiri
                         ->update(['is_active' => false]);
            }
            // Jika 'Simpan Permanen', nonaktifkan semua jadwal aktif sebelumnya
            else {
                 Schedule::where('is_active', true)->where('id', '!=', $schedule->id)
                         ->update(['is_active' => false]);
            }

            // 2. Update Jadwal Induk
            $schedule->update([
                'nama_jadwal' => $validated['nama_jadwal'],
                'is_permanent' => $validated['is_permanent'],
                'is_active' => true, // Selalu aktif saat diperbarui
            ]);

            // 3. Hapus semua entri lama
            $schedule->entries()->delete();

            // 4. Buat ulang semua Entri Detail
            foreach ($validated['entries'] as $entry) {
                ScheduleEntry::create([
                    'schedule_id' => $schedule->id,
                    'matakuliah_kode_mk' => $entry['mk'],
                    'dosen_kd' => $entry['dosen'],
                    'hari' => $entry['hari'],
                    'jam_slot' => $entry['jam'],
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Jadwal berhasil diperbarui!']);
    }

    /**
     * Hapus jadwal (dari halaman History)
     */
    public function destroy(Schedule $schedule)
    {
        // Relasi 'entries' akan terhapus otomatis karena 'onDelete('cascade')'
        $schedule->delete();

        return redirect()->route('jadwal.history')
                         ->with('success', 'Jadwal berhasil dihapus permanen.');
    }

    /**
     * Mengunduh jadwal "Unduh Semua" sebagai PDF.
     */
    public function downloadSemua(Schedule $schedule)
    {
        // 1. Ambil data (sama seperti sebelumnya)
        $schedule->load('entries.matakuliah', 'entries.dosen');
        
        // 2. Tentukan nama file
        $fileName = 'jadwal_semua_' . $schedule->nama_jadwal . '.pdf';

        // 3. Render view-nya menjadi HTML
        $pdf = Pdf::loadView('unduh.jadwal-semua', ['schedule' => $schedule]);
        
        // 4. Atur menjadi landscape (karena tabelnya lebar)
        $pdf->setPaper('a4', 'landscape');

        // 5. Download file
        return $pdf->download($fileName);
    }

    /**
     * Mengunduh jadwal "Unduh Per Dosen" sebagai PDF.
     */
    public function downloadPerDosen(Schedule $schedule)
    {
        // 1. Ambil data dan kelompokkan (sama seperti sebelumnya)
        $entries = $schedule->entries()->with('matakuliah', 'dosen')->get();
        $jadwalPerDosen = $entries->groupBy('dosen_kd');
        
        // 2. Tentukan nama file
        $fileName = 'jadwal_per_dosen_' . $schedule->nama_jadwal . '.pdf';

        // 3. Render view-nya menjadi HTML
        $pdf = Pdf::loadView('unduh.jadwal-dosen', [
            'schedule' => $schedule,
            'jadwalPerDosen' => $jadwalPerDosen
        ]);

        // 4. Atur menjadi landscape
        $pdf->setPaper('a4', 'landscape');

        // 5. Download file
        return $pdf->download($fileName);
    }
}