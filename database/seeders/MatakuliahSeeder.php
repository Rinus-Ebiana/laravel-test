<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matakuliah;
use Illuminate\Support\Facades\DB;

class MatakuliahSeeder extends Seeder
{
    public function run(): void
    {
        // (Perintah truncate sudah dipindah ke DatabaseSeeder.php)

        // Data matakuliah (tanpa dosen)
        $matakuliahData = [
            // --- SEMESTER 1 ---
            ['kode_mk' => 'M1241101', 'nama_mk' => 'Advance Machine Learning', 'sks' => 3, 'semester' => 1],
            ['kode_mk' => 'M1241102', 'nama_mk' => 'Advance Security System', 'sks' => 3, 'semester' => 1],
            ['kode_mk' => 'M1241103', 'nama_mk' => 'Big Data Analytic', 'sks' => 3, 'semester' => 1],
            ['kode_mk' => 'M1241114', 'nama_mk' => 'Teknologi dan Digitalisasi Budaya', 'sks' => 3, 'semester' => 1],
            ['kode_mk' => 'M1241105', 'nama_mk' => 'Digital Enterpreneur', 'sks' => 3, 'semester' => 1],
            ['kode_mk' => 'M1241107', 'nama_mk' => 'Scientific Metodologi Penelitian', 'sks' => 3, 'semester' => 1],
            
            // --- SEMESTER 2 ---
            ['kode_mk' => 'M1241104', 'nama_mk' => 'Desain Riset', 'sks' => 3, 'semester' => 2],
            ['kode_mk' => 'M1242106', 'nama_mk' => 'Deep Learning', 'sks' => 3, 'semester' => 2], // MK Konsentrasi 1
            ['kode_mk' => 'M1242109', 'nama_mk' => 'Intelligence, Security And Infosphere', 'sks' => 3, 'semester' => 2], // MK Konsentrasi 2
            ['kode_mk' => 'M1242111', 'nama_mk' => 'System Enterprise Mutakhir', 'sks' => 3, 'semester' => 2], // MK Konsentrasi 3
            ['kode_mk' => 'M1241110', 'nama_mk' => 'Transformasi IS', 'sks' => 3, 'semester' => 2],
            ['kode_mk' => 'M1241108', 'nama_mk' => 'Teknik Publikasi Ilmiah', 'sks' => 3, 'semester' => 2],
            
            // --- SEMESTER 3 (DIPERBARUI) ---
            ['kode_mk' => 'M1241111', 'nama_mk' => 'Publikasi', 'sks' => 4, 'semester' => 3],
            ['kode_mk' => 'M1241113', 'nama_mk' => 'Seminar Tesis', 'sks' => 4, 'semester' => 3],
            ['kode_mk' => 'M1241106', 'nama_mk' => 'Proposal Tesis', 'sks' => 4, 'semester' => 3], // Pindah dari Sem 2
            
            // --- SEMESTER 4 (DIPERBARUI) ---
            ['kode_mk' => 'M1241109', 'nama_mk' => 'Tesis', 'sks' => 8, 'semester' => 4], // Pindah dari Sem 3
        ];

        // Buat Matakuliah
        foreach ($matakuliahData as $data) {
            Matakuliah::create($data);
        }

        // Data Relasi Dosen (Tidak berubah, kecuali Proposal Tesis)
        $relasi = [
            'M1241101' => ['GAP'],
            'M1241102' => ['RRH'],
            'M1241103' => ['DPH'],
            'M1241114' => ['DPH', 'GAP'],
            'M1241105' => ['DH'],
            'M1241107' => ['DPH'],
            'M1241104' => ['DPH'],
            'M1242106' => ['GAP'],
            'M1241108' => ['GAP', 'DPH'],
            'M1242109' => ['RRH'],
            'M1241110' => ['ET'],
            'M1242111' => ['NPS'],
            'M1241106' => ['DPH', 'RRH'], // Relasi Proposal Tesis
        ];

        // Lampirkan (Attach) Dosen ke Matakuliah
        foreach ($relasi as $kode_mk => $dosen_kds) {
            $matakuliah = Matakuliah::find($kode_mk);
            if ($matakuliah) {
                $matakuliah->dosen()->attach($dosen_kds);
            }
        }
    }
}