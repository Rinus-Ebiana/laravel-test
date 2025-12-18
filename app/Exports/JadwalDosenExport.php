<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class JadwalDosenExport implements WithMultipleSheets
{
    protected $jadwalPerDosen;
    protected $schedule;

    public function __construct($jadwalPerDosen, $schedule)
    {
        $this->jadwalPerDosen = $jadwalPerDosen;
        $this->schedule = $schedule;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->jadwalPerDosen as $kd_dosen => $entries) {
            $sheets[] = new JadwalDosenExportSheet($kd_dosen, $entries);
        }

        return $sheets;
    }
}

class JadwalDosenExportSheet implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\ShouldAutoSize
{
    protected $kd_dosen;
    protected $entries;

    public function __construct($kd_dosen, $entries)
    {
        $this->kd_dosen = $kd_dosen;
        $this->entries = $entries;
    }

    public function title(): string
    {
        return $this->entries->first()->dosen->nama . ' (' . $this->kd_dosen . ')';
    }

    public function headings(): array
    {
        return [
            'Kode MK',
            'Matakuliah',
            'SKS',
            'Semester',
            'Hari',
            'Jam'
        ];
    }

    public function collection()
    {
        return $this->entries->sortBy('matakuliah.semester')->map(function ($entry) {
            return [
                $entry->matakuliah->kode_mk,
                $entry->matakuliah->nama_mk,
                $entry->matakuliah->sks,
                $entry->matakuliah->semester,
                $entry->hari,
                $entry->jam_slot
            ];
        });
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        // Header tebal & tengah
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Border semua data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:F{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                ],
            ],
        ]);

        // Kolom tertentu center
        $sheet->getStyle("C2:C{$lastRow}")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("D2:D{$lastRow}")->getAlignment()->setHorizontal('center');

        // Set row height to auto for all rows
        for ($row = 1; $row <= $lastRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(-1);
        }
    }
}
