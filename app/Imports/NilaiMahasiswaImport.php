<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use App\Models\Matakuliah;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class NilaiMahasiswaImport implements ToCollection, WithHeadingRow
{
    protected $mahasiswa;

    public function __construct(Mahasiswa $mahasiswa)
    {
        $this->mahasiswa = $mahasiswa;
    }

    public function collection(Collection $rows)
    {
        $syncData = [];

        \Log::info('Starting import for mahasiswa: ' . $this->mahasiswa->nim . ' (semester: ' . $this->mahasiswa->semester . ')');
        \Log::info('Rows received: ' . $rows->count());

        foreach ($rows as $index => $row) {
            \Log::info("Processing row {$index}: " . json_encode($row));

            // Skip empty rows
            if (empty($row['kode_mk']) || empty($row['nilai'])) {
                \Log::info("Skipping row {$index}: empty kode_mk or nilai");
                continue;
            }

            $kodeMk = trim($row['kode_mk']);
            $nilai = trim(strtoupper($row['nilai']));

            \Log::info("Row {$index}: kode_mk='{$kodeMk}', nilai='{$nilai}'");

            // Validate nilai
            $validNilai = ['A', 'AB', 'B', 'BC', 'C', 'D', 'E'];
            if (!in_array($nilai, $validNilai)) {
                \Log::info("Skipping row {$index}: invalid nilai '{$nilai}'");
                continue; // Skip invalid nilai
            }

            // Check if matakuliah exists
            $matakuliah = Matakuliah::where('kode_mk', $kodeMk)->first();
            if (!$matakuliah) {
                \Log::info("Skipping row {$index}: matakuliah '{$kodeMk}' not found");
                continue; // Skip if matakuliah doesn't exist
            }

            \Log::info("Matakuliah found: {$matakuliah->nama_mk} (semester: {$matakuliah->semester})");

            // Check if mahasiswa semester >= matakuliah semester
            if ($this->mahasiswa->semester < $matakuliah->semester) {
                \Log::info("Skipping row {$index}: mahasiswa semester ({$this->mahasiswa->semester}) < matakuliah semester ({$matakuliah->semester})");
                continue; // Skip if not eligible
            }

            $syncData[$kodeMk] = ['nilai' => $nilai];
            \Log::info("Added to syncData: {$kodeMk} => {$nilai}");
        }

        \Log::info('Final syncData: ' . json_encode($syncData));

        // Sync the data - only sync if we have valid data to prevent clearing existing data
        if (!empty($syncData)) {
            $result = $this->mahasiswa->nilaiMatakuliah()->syncWithoutDetaching($syncData);
            \Log::info('Sync completed. Result: ' . json_encode($result));

            // Verify the data was saved
            $savedData = $this->mahasiswa->nilaiMatakuliah()->whereIn('kode_mk', array_keys($syncData))->get();
            \Log::info('Verified saved data: ' . $savedData->toJson());
        } else {
            \Log::info('No valid data to sync');
        }
    }
}
