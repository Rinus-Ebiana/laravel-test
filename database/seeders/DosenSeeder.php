<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dosen;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        // Dosen::truncate(); // <-- PASTIKAN BARIS INI SUDAH DIHAPUS

        $dosenData = [
            [
                'kd' => 'GAP',
                'nama' => 'Dr. GEDE ANGGA PRADIPTA, S.T., M.Eng',
                'nip' => '14.88.229',
                'no_telp' => '087737464858',
                'email' => 'angga_pradipta@stikom-bali.ac.id',
            ],
            [
                'kd' => 'DPH',
                'nama' => 'Dr. DANDY PRAMANA HOSTIADI, S.Kom., M.T.',
                'nip' => '10.87.147',
                'no_telp' => '081934365434',
                'email' => 'dandy@stikom-bali.ac.id',
            ],
            [
                'kd' => 'RRH',
                'nama' => 'Dr. ROY RUDOLF HUIZEN, S.T., M.T.',
                'nip' => '08.75.056',
                'no_telp' => '082145212222',
                'email' => 'roy@stikom-bali.ac.id',
            ],
            [
                'kd' => 'DH',
                'nama' => 'Dr. DADANG HERMAWAN, S.E., M.M., Ak.',
                'nip' => '02.63.001',
                'no_telp' => '081337679999',
                'email' => 'dadang@stikom-bali.ac.id',
            ],
            [
                'kd' => 'NPS',
                'nama' => 'Dr. NI LUH PUTRI SRINADI, SE., MM.Kom',
                'nip' => '02.67.002',
                'no_telp' => '08123602702',
                'email' => 'putri@stikom-bali.ac.id',
            ],
            [
                'kd' => 'ET',
                'nama' => 'Dr. EVI TRIANDINI, S.P., M.Eng',
                'nip' => '04.70.013',
                'no_telp' => '08123890008',
                'email' => 'evi@stikom-bali.ac.id',
            ],
        ];

        foreach ($dosenData as $data) {
            Dosen::create($data);
        }
    }
}