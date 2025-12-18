<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class TemplateNilaiExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'kode_mk',
            'nilai'
        ];
    }

    public function collection(): Collection
    {
        return collect([
            ['', ''], // Empty row for user to fill
            ['', ''], // Empty row for user to fill
            ['', ''], // Empty row for user to fill
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '0D6EFD']
            ],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Data rows styling
        $sheet->getStyle('A2:B4')->applyFromArray([
            'alignment' => ['horizontal' => 'center'],
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin']
            ]
        ]);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(10);

        return [];
    }
}
