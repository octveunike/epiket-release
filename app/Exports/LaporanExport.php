<?php

namespace App\Exports;

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

class LaporanExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected array $rows;
    protected array $filters;

    public function __construct(array $rows, array $filters)
    {
        $this->rows    = $rows;
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Laporan Kehadiran';
    }

    public function headings(): array
    {
        return ['No', 'Hari', 'Tanggal', 'Nama Siswa', 'Kelas', 'Kategori', 'Deskripsi', 'Keterangan', 'Penginput'];
    }

    public function array(): array
    {
        return collect($this->rows)->map(function ($row, $i) {
            return [
                $i + 1,
                $row['hari'],
                $row['tanggal'],
                $row['nama_siswa'],
                $row['kelas'],
                $row['kategori'],
                $row['deskripsi'],
                $row['keterangan'],
                $row['penginput'],
            ];
        })->toArray();
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 12,
            'C' => 22,
            'D' => 30,
            'E' => 12,
            'F' => 16,
            'G' => 30,
            'H' => 30,
            'I' => 22,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = count($this->rows) + 2; // +1 header info row, +1 heading row

        return [
            // Header row styling
            2 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2D6A4F']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Info row (row 1)
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
                $lastRow = count($this->rows) + 2;

                // ── Info baris pertama ────────────────────────
                $dari    = $this->filters['dari']    ?? '-';
                $sampai  = $this->filters['sampai']  ?? '-';
                $kategori = $this->filters['kategori'] ? ucfirst($this->filters['kategori']) : 'Semua';

                $sheet->insertNewRowBefore(1, 1);
                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', "Laporan Kehadiran Siswa | Periode: {$dari} s/d {$sampai} | Kategori: {$kategori}");
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FF1B4332']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD8F3DC']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(24);

                // ── Heading row (baris 2) ─────────────────────
                $sheet->getRowDimension(2)->setRowHeight(20);

                // ── Data rows: border + zebra stripe ─────────
                $borderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFD1D5DB'],
                        ],
                    ],
                ];

                for ($r = 3; $r <= $lastRow + 1; $r++) {
                    // Zebra
                    if ($r % 2 === 0) {
                        $sheet->getStyle("A{$r}:I{$r}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF9FAFB']],
                        ]);
                    }
                    $sheet->getStyle("A{$r}:I{$r}")->applyFromArray($borderStyle);
                    $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Heading border juga
                $sheet->getStyle('A2:I2')->applyFromArray($borderStyle);

                // Freeze header
                $sheet->freezePane('A3');

                // Auto filter
                $sheet->setAutoFilter("A2:I2");
            },
        ];
    }
}