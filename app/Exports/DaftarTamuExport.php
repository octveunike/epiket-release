<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DaftarTamuExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected Collection $rows;
    protected array $filters;

    public function __construct(Collection $rows, array $filters = [])
    {
        $this->rows    = $rows;
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Daftar Tamu';
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Nama', 'Lembaga / Organisasi', 'Orang Dituju', 'Tujuan'];
    }

    public function array(): array
    {
        return $this->rows->values()->map(function ($row, $i) {
            return [
                $i + 1,
                $row->tanggal_kunjungan ? Carbon::parse($row->tanggal_kunjungan)->format('d M Y') : '-',
                $row->nama ?? '-',
                $row->lembaga_organisasi ?? '-',
                $row->orang_yang_dituju ?? '-',
                $row->tujuan_kunjungan ?? '-',
            ];
        })->toArray();
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 16,
            'C' => 28,
            'D' => 28,
            'E' => 24,
            'F' => 36,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            2 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2D6A4F']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            1 => [
                'font'      => ['bold' => true, 'size' => 11],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $this->rows->count() + 2;

                $dari   = !empty($this->filters['dari'])   ? Carbon::parse($this->filters['dari'])->format('d M Y')   : '-';
                $sampai = !empty($this->filters['sampai']) ? Carbon::parse($this->filters['sampai'])->format('d M Y') : '-';

                $sheet->insertNewRowBefore(1, 1);
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', "Daftar Tamu | Periode: {$dari} s/d {$sampai}");
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FF1B4332']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD8F3DC']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(24);

                $sheet->getRowDimension(2)->setRowHeight(20);

                $borderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFD1D5DB'],
                        ],
                    ],
                ];

                for ($r = 3; $r <= $lastRow + 1; $r++) {
                    if ($r % 2 === 0) {
                        $sheet->getStyle("A{$r}:F{$r}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF9FAFB']],
                        ]);
                    }
                    $sheet->getStyle("A{$r}:F{$r}")->applyFromArray($borderStyle);
                    $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $sheet->getStyle('A2:F2')->applyFromArray($borderStyle);
                $sheet->freezePane('A3');
                $sheet->setAutoFilter('A2:F2');
            },
        ];
    }
}
