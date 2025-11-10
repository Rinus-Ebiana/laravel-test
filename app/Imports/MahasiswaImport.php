<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
// 1. Impor trait dan interface yang baru
use Maatwebsite\Excel\Concerns\SkipsOnFailure; // <-- PENTING
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures; // <-- PENTING

// 2. Tambahkan SkipsOnFailure
class MahasiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    // 3. Tambahkan trait SkipsFailures
    use SkipsFailures; 

    public function model(array $row)
    {
        // Kode ini hanya akan berjalan jika validasi lolos
        
        $nim = $row['nim'];
        $email = null;
        if (!empty($nim)) {
            $email = $nim . '@stikom-bali.ac.id';
        }

        $tahunMasukString = $row['tahun_masuk'] ?? '';
        
        $semester_masuk_awal = null;
        if (str_contains(strtoupper($tahunMasukString), 'T.A. GANJIL')) {
            $semester_masuk_awal = 1;
        } elseif (str_contains(strtoupper($tahunMasukString), 'T.A. GENAP')) {
            $semester_masuk_awal = 2;
        }

        $tahun_masuk_awal = null;
        if (preg_match('/(\d{4})\/\d{4}/', $tahunMasukString, $matches)) {
            $tahun_masuk_awal = (int) $matches[1];
        }

        return new Mahasiswa([
            'nim'                 => $nim,
            'nama'                => $row['nama'],
            'tahun_masuk_awal'    => $tahun_masuk_awal,
            'semester_masuk_awal' => $semester_masuk_awal,
            'no_telp'             => $row['no_telp'],
            'email'               => $email,
        ]);
    }

    public function rules(): array
    {
        return [
            // Aturan ini tetap ada, tapi sekarang akan dilewati jika gagal
            'nim' => 'required|unique:mahasiswa,nim',
            'nama' => 'required',
        ];
    }
    
    // 4. Tambahkan fungsi onFailure (wajib ada jika pakai SkipsOnFailure)
    public function onFailure(Failure ...$failures)
    {
        // Biarkan kosong.
        // Ini memberitahu Laravel: "Jika ada error, lewati saja baris itu."
    }
}