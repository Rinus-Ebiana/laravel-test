<?php

// database/seeders/MahasiswaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <-- Tambahkan ini
use Maatwebsite\Excel\Facades\Excel; // <-- Tambahkan ini
use App\Imports\MahasiswaImport; // <-- Tambahkan ini

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {

        // Tentukan path file di dalam storage
        $filePath = storage_path('app/seeders/mahasiswa.xlsx');

        // Jalankan import
        Excel::import(new MahasiswaImport, $filePath);
    }
}