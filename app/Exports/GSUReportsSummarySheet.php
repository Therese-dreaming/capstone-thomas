<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class GSUReportsSummarySheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents
{
    /** @var \Illuminate\Support\Collection */
    protected Collection $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection(): Collection
    {
        $summary = collect();
        
        // Total reports
        $summary->push([
            'metric' => 'Total Reports',
            'count' => $this->rows->count()
        ]);
        
        // Status breakdown
        $statusCounts = $this->rows->groupBy('status')->map->count();
        
        foreach ($statusCounts as $status => $count) {
            $summary->push([
                'metric' => ucfirst($status) . ' Reports',
                'count' => $count
            ]);
        }
        
        // Type breakdown
        $typeCounts = $this->rows->groupBy('type')->map->count();
        foreach ($typeCounts as $type => $count) {
            $summary->push([
                'metric' => ucfirst($type) . ' Issues',
                'count' => $count
            ]);
        }
        
        // Severity breakdown
        $severityCounts = $this->rows->groupBy('severity')->map->count();
        foreach ($severityCounts as $severity => $count) {
            $summary->push([
                'metric' => ucfirst($severity) . ' Severity',
                'count' => $count
            ]);
        }
        
        // Department breakdown
        $deptCounts = $this->rows->groupBy('reportedUser.department')->map->count();
        foreach ($deptCounts as $dept => $count) {
            if ($dept) {
                $summary->push([
                    'metric' => $dept . ' Department',
                    'count' => $count
                ]);
            }
        }
        
        return $summary;
    }

    public function headings(): array
    {
        return [
            'Metric',
            'Count'
        ];
    }

    public function map($item): array
    {
        return [
            $item['metric'],
            $item['count']
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,  // Metric
            'B' => 15,  // Count
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '800000']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            2 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => '000000']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F3F4F6']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Add header information
                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', 'GSU REPORTS SUMMARY');
                $sheet->mergeCells('A1:B1');
                $sheet->setCellValue('A2', 'Generated on: ' . now()->format('F j, Y \a\t g:i A'));
                $sheet->mergeCells('A2:B2');
                
                // Set header row heights
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(25);
                $sheet->getRowDimension(3)->setRowHeight(25); // Column headers
                
                // Style the main header
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '800000']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
                
                // Style the date header
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => '000000']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3F4F6']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
                
                // Add borders to all cells
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                
                $range = 'A1:' . $lastColumn . $lastRow;
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
                
                // Set data row heights
                for ($row = 4; $row <= $lastRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(25);
                }
                
                // Center align count column
                $sheet->getStyle('B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Style the first data row (Total Reports) differently
                if ($lastRow >= 4) {
                    $sheet->getStyle('A4:B4')->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'E5E7EB']
                        ]
                    ]);
                }
            }
        ];
    }
}
