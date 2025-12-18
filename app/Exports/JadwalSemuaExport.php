<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JadwalSemuaExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $schedule;

    public function __construct($schedule)
    {
        $this->schedule = $schedule;
    }

    public function title(): string
    {
        return 'Jadwal Semua';
    }

    public function headings(): array
    {
        return [
            'Kode MK',
            'Matakuliah',
            'SKS',
            'Semester',
            'Dosen Pengampu',
            'Hari',
            'Jam'
        ];
    }

    public function collection()
    {
        return $this->schedule->entries->sortBy('matakuliah.semester')->map(function ($entry) {
            return [
                $entry->matakuliah->kode_mk,
                $entry->matakuliah->nama_mk,
                $entry->matakuliah->sks,
                $entry->matakuliah->semester,
                $entry->dosen->nama . ' (' . $entry->dosen->kd . ')',
                $entry->hari,
                $entry->jam_slot
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        // Header tebal & tengah
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Border semua data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:G{$lastRow}")->applyFromArray([
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
