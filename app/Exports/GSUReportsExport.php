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
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class GSUReportsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents, WithMultipleSheets
{
    /** @var \Illuminate\Support\Collection */
    protected Collection $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Report ID',
            'Issue Type',
            'Severity',
            'Status',
            'Reported User',
            'User Email',
            'User Department',
            'Reporter Name',
            'Description',
            'Actions Taken'
        ];
    }

    public function map($report): array
    {
        return [
            $report->id,
            ucfirst($report->type),
            ucfirst($report->severity),
            ucfirst($report->status),
            $report->reportedUser->name ?? 'N/A',
            $report->reportedUser->email ?? 'N/A',
            $report->reportedUser->department ?? 'N/A',
            $report->reporter->name ?? 'N/A',
            $report->description,
            $report->actions_taken ?? 'N/A',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Report ID
            'B' => 15,  // Issue Type
            'C' => 12,  // Severity
            'D' => 12,  // Status
            'E' => 25,  // Reported User
            'F' => 30,  // User Email
            'G' => 30,  // User Department
            'H' => 30,  // Reporter Name
            'I' => 40,  // Description
            'J' => 40,  // Actions Taken
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
                $sheet->setCellValue('A1', 'GSU REPORTS EXPORT');
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A2', 'Generated on: ' . now()->format('F j, Y \a\t g:i A'));
                $sheet->mergeCells('A2:J2');
                
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
                
                // Set data row heights (starting from row 4)
                for ($row = 4; $row <= $lastRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(35);
                }
                
                // Auto-wrap text for description and actions columns
                $sheet->getStyle('I:J')->getAlignment()->setWrapText(true);
                
                // Center align ID columns
                $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Color-code status cells
                $this->applyStatusColors($sheet, $lastRow);
            }
        ];
    }
    
    private function applyStatusColors($sheet, $lastRow)
    {
        // Status column is D (4th column)
        for ($row = 4; $row <= $lastRow; $row++) {
            $statusCell = 'D' . $row;
            $statusValue = $sheet->getCell($statusCell)->getValue();
            
            $color = $this->getStatusColor($statusValue);
            if ($color) {
                $sheet->getStyle($statusCell)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $color]
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '000000']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
            }
        }
    }
    
    private function getStatusColor($status)
    {
        switch (strtolower($status)) {
            case 'pending':
                return 'F59E0B'; // Yellow
            case 'investigating':
                return '3B82F6'; // Blue
            case 'resolved':
                return '10B981'; // Green
            default:
                return null;
        }
    }
    
    public function sheets(): array
    {
        return [
            'Reports' => $this,
            'Summary' => new GSUReportsSummarySheet($this->rows),
        ];
    }
}
