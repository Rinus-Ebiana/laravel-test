<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema; // <-- PENTING
use Illuminate\Support\Facades\DB;       // <-- PENTING

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Nonaktifkan pengecekan foreign key
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel dalam urutan yang BENAR
        //    (Selalu tabel "anak" / "pivot" terlebih dahulu)
        DB::table('dosen_matakuliah')->truncate();
        
        // 3. Baru kosongkan tabel "parent" dan sisanya
        DB::table('matakuliah')->truncate();
        DB::table('dosen')->truncate();
        DB::table('mahasiswa')->truncate();
        DB::table('users')->truncate();
        
        // 4. Aktifkan kembali pengecekan foreign key
        Schema::enableForeignKeyConstraints();

        // 5. Panggil semua seeder (yang sekarang HANYA mengisi data)
        $this->call([
            UserSeeder::class,
            DosenSeeder::class,
            MatakuliahSeeder::class,
            MahasiswaSeeder::class,
        ]);
    }
}