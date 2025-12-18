<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KelasExport implements WithMultipleSheets
{
    protected $schedule;
    protected $entriesBySemester;

    public function __construct($schedule, $entriesBySemester)
    {
        $this->schedule = $schedule;
        $this->entriesBySemester = $entriesBySemester;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->entriesBySemester as $semester => $entries) {
            $sheets[] = new KelasExportSheet($semester, $entries);
        }

        return $sheets;
    }
}

class KelasExportSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $semester;
    protected $entries;

    public function __construct($semester, $entries)
    {
        $this->semester = $semester;
        $this->entries = $entries;
    }

    public function title(): string
    {
        return 'Semester ' . $this->semester;
    }

    public function headings(): array
    {
        return [
            'Kode MK',
            'Matakuliah',
            'SKS',
            'Dosen Pengampu',
            'Hari',
            'Jam',
            'Kelas',
            'Ruang'
        ];
    }

    public function collection()
    {
        return $this->entries->map(function ($entry) {
            return [
                $entry->matakuliah->kode_mk,
                $entry->matakuliah->nama_mk,
                $entry->matakuliah->sks,
                $entry->dosen->nama . ' (' . $entry->dosen->kd . ')',
                $entry->hari,
                $entry->jam_slot,
                $entry->kode_kelas,
                $entry->ruang_kelas
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        // Header tebal & tengah
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Border semua data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:H{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                ],
            ],
        ]);

        // Kolom tertentu center
        $sheet->getStyle("C2:C{$lastRow}")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("G2:G{$lastRow}")->getAlignment()->setHorizontal('center');

        // Set row height to auto for all rows
        for ($row = 1; $row <= $lastRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(-1);
        }
    }
}
