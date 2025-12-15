<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Nonaktifkan pengecekan foreign key
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel dalam urutan yang BENAR
        //    (Tabel "child" / "pivot" / "transaksi" harus dihapus duluan)
        
        // Tabel Level 3 (Paling Bawah)
        DB::table('krs_records')->truncate(); 
        
        // Tabel Level 2 (Bergantung pada tabel lain)
        DB::table('schedule_entries')->truncate();
        DB::table('mahasiswa_matakuliah')->truncate();
        DB::table('dosen_matakuliah')->truncate();
        
        // Tabel Level 1 (Induk)
        DB::table('schedules')->truncate();
        DB::table('matakuliah')->truncate();
        DB::table('dosen')->truncate();
        DB::table('mahasiswa')->truncate();
        DB::table('users')->truncate();
        
        // 3. Aktifkan kembali pengecekan foreign key
        Schema::enableForeignKeyConstraints();

        // 4. Panggil semua seeder
        $this->call([
            UserSeeder::class,
            DosenSeeder::class,
            MatakuliahSeeder::class,
            MahasiswaSeeder::class,
            // (Jadwal, Kelas, dan KRS tidak punya seeder khusus karena datanya dinamis)
        ]);
    }
}