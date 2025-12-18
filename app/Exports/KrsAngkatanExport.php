<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KrsAngkatanExport implements FromArray, WithStyles, ShouldAutoSize, WithTitle, WithStartRow
{
    protected $students;
    protected $krs_map;
    protected $nama_angkatan;

    public function __construct($students, $krs_map, $nama_angkatan)
    {
        $this->students = $students;
        $this->krs_map = $krs_map;
        $this->nama_angkatan = $nama_angkatan;
    }

    public function title(): string
    {
        return 'KRS Angkatan';
    }

    public function startRow(): int
    {
        return 3;
    }

    public function array(): array
    {
        $rows = [];

        // ===== ROW 1 : TITLE =====
        $rows[] = ['KRS Angkatan: ' . $this->nama_angkatan];

        // ===== ROW 2 : HEADER =====
        $rows[] = [
            'No','NIM','Nama Mahasiswa','Kode MK','Matakuliah','SKS',
            'Dosen','Hari','Jam','Kelas','Ruang'
        ];

        $no = 1;

        foreach ($this->students as $student) {
            $krs_records = $this->krs_map->get($student->nim);

            if (!$krs_records || $krs_records->isEmpty()) {
                $rows[] = [
                    $no++,
                    $student->nim,
                    $student->nama,
                    '-',
                    'Belum menyusun KRS',
                    '-','-','-','-','-','-'
                ];
                continue;
            }

            $first = true;

            foreach ($krs_records as $krs) {
                $matakuliah = null;
                $dosen = $hari = $jam = $kelas = $ruang = '-';

                if ($krs->scheduleEntry) {
                    $matakuliah = $krs->scheduleEntry->matakuliah;
                    $dosen = $krs->scheduleEntry->dosen->nama ?? '-';
                    $hari  = $krs->scheduleEntry->hari ?? '-';
                    $jam   = $krs->scheduleEntry->jam_slot ?? '-';
                    $kelas = $krs->scheduleEntry->kode_kelas ?? '-';
                    $ruang = $krs->scheduleEntry->ruang_kelas ?? '-';
                } elseif ($krs->matakuliah) {
                    $matakuliah = $krs->matakuliah;
                }

                $rows[] = [
                    $first ? $no++ : '',
                    $first ? $student->nim : '',
                    $first ? $student->nama : '',
                    $matakuliah->kode_mk ?? '-',
                    $matakuliah->nama_mk ?? '-',
                    $matakuliah->sks ?? '-',
                    $dosen,
                    $hari,
                    $jam,
                    $kelas,
                    $ruang,
                ];

                $first = false;
            }
        }

        return $rows;
    }


    public function styles(Worksheet $sheet)
    {
        // Title
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => 'center'],
        ]);

        $lastRow = $sheet->getHighestRow();

        // Header
        $sheet->getStyle('A2:K2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '0D6EFD']
            ],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Borders
        $sheet->getStyle("A2:K{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => 'thin']]
        ]);

        // ================= WARNA PER MAHASISWA =================
        $lastRow = $sheet->getHighestRow();

        $colors = [
            'FFF2CC', // kuning lembut
            'E2EFDA', // hijau lembut
            'DDEBF7', // biru lembut
            'FCE4D6', // oranye lembut
            'E4DFEC', // ungu lembut
        ];

        $colorIndex = -1;
        $currentNim = null;

        for ($row = 3; $row <= $lastRow; $row++) {
            $nim = trim((string) $sheet->getCell("B{$row}")->getValue());

            // Jika ketemu NIM baru → ganti warna
            if ($nim !== '' && $nim !== $currentNim) {
                $currentNim = $nim;
                $colorIndex = ($colorIndex + 1) % count($colors);
            }

            // Terapkan warna (SEMUA baris mahasiswa tsb)
            $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => $colors[$colorIndex]],
                ],
            ]);

            $sheet->getRowDimension($row)->setRowHeight(-1);
        }

    }
}
