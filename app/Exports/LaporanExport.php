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
        return ['No', 'Hari', 'Tanggal', 'Nama Siswa', 'Kelas', 'Kategori', 'Deskripsi', 'Update Terakhir'];
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
            'H' => 22,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // All styling is applied in the AfterSheet event so it runs
        // against stable post-insert row positions.
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ── Info baris pertama ────────────────────────
                $dari   = $this->filters['dari']   ?? '-';
                $sampai = $this->filters['sampai'] ?? '-';

                $headerText = "Laporan Kehadiran Siswa | Periode: {$dari} s/d {$sampai}";
                if (!empty($this->filters['kategori'])) {
                    $headerText .= ' | Kategori: ' . ucfirst($this->filters['kategori']);
                }

                $sheet->insertNewRowBefore(1, 1);
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', $headerText);
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FF1B4332']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD8F3DC']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(24);

                // ── Heading row (baris 2) ─────────────────────
                $sheet->getStyle('A2:H2')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2D6A4F']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
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

                $lastRow = count($this->rows) + 2;
                for ($r = 3; $r <= $lastRow; $r++) {
                    if ($r % 2 === 0) {
                        $sheet->getStyle("A{$r}:H{$r}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF9FAFB']],
                        ]);
                    }
                    $sheet->getStyle("A{$r}:H{$r}")->applyFromArray($borderStyle);
                    $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Heading border juga
                $sheet->getStyle('A2:H2')->applyFromArray($borderStyle);

                // Freeze header
                $sheet->freezePane('A3');

                // Auto filter
                $sheet->setAutoFilter("A2:H2");
            },
        ];
    }
}