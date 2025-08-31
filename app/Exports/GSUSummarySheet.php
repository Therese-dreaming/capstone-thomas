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
use PhpOffice\PhpSpreadsheet\Style\Color;

class GSUSummarySheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents
{
    /** @var \Illuminate\Support\Collection */
    protected Collection $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection(): Collection
    {
        // Create summary data
        $summaryData = collect();
        
        // Add summary statistics
        $summaryData->push([
            'Metric' => 'Total Reservations',
            'Value' => $this->rows->count(),
            'Description' => 'Total number of final approved reservations'
        ]);
        
        $totalRevenue = $this->rows->sum('final_price');
        $summaryData->push([
            'Metric' => 'Total Revenue',
            'Value' => '₱' . number_format($totalRevenue, 2),
            'Description' => 'Total revenue from all reservations'
        ]);
        
        $totalDuration = $this->rows->sum(function($r) {
            if ($r->start_date && $r->end_date) {
                return \Carbon\Carbon::parse($r->start_date)->diffInHours($r->end_date);
            }
            return 0;
        });
        $avgDuration = $this->rows->count() > 0 ? round($totalDuration / $this->rows->count(), 1) : 0;
        $summaryData->push([
            'Metric' => 'Average Duration',
            'Value' => $avgDuration . ' hours',
            'Description' => 'Average duration per reservation'
        ]);
        
        $summaryData->push([
            'Metric' => 'Total Duration',
            'Value' => $totalDuration . ' hours',
            'Description' => 'Total hours across all reservations'
        ]);
        
        // Add date range info if filters were applied
        if (request('start_date') || request('end_date')) {
            $dateRange = '';
            if (request('start_date') && request('end_date')) {
                $dateRange = request('start_date') . ' to ' . request('end_date');
            } elseif (request('start_date')) {
                $dateRange = 'From ' . request('start_date');
            } elseif (request('end_date')) {
                $dateRange = 'Until ' . request('end_date');
            }
            
            $summaryData->push([
                'Metric' => 'Date Range',
                'Value' => $dateRange,
                'Description' => 'Filtered date range for this export'
            ]);
        }
        
        // Add venue statistics
        $venueStats = $this->rows->groupBy('venue.name')->map(function($group) {
            return [
                'Metric' => 'Venue: ' . $group->first()->venue->name,
                'Value' => $group->count() . ' reservations',
                'Description' => 'Total reservations for this venue'
            ];
        });
        
        foreach ($venueStats as $venueStat) {
            $summaryData->push($venueStat);
        }
        
        // Add equipment statistics
        $equipmentCount = 0;
        foreach ($this->rows as $row) {
            if (is_array($row->equipment_details)) {
                $equipmentCount += count($row->equipment_details);
            }
        }
        
        $summaryData->push([
            'Metric' => 'Total Equipment Items',
            'Value' => $equipmentCount,
            'Description' => 'Total equipment items requested across all reservations'
        ]);
        
        return $summaryData;
    }

    public function headings(): array
    {
        return [
            'Metric',
            'Value',
            'Description'
        ];
    }

    public function map($row): array
    {
        return [
            $row['Metric'],
            $row['Value'],
            $row['Description']
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,  // Metric
            'B' => 25,  // Value
            'C' => 50,  // Description
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFont()->setSize(12);
        $sheet->getStyle('A1:C1')->getFont()->setColor(new Color(Color::COLOR_WHITE));
        
        // Center align headers
        $sheet->getStyle('A1:C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:C1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                
                // Add title row at the top
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:C1');
                $sheet->setCellValue('A1', 'GSU RESERVATION SYSTEM - SUMMARY REPORT');
                $sheet->getStyle('A1')->getFont()->setBold(true);
                $sheet->getStyle('A1')->getFont()->setSize(16);
                $sheet->getStyle('A1')->getFont()->setColor(new Color(Color::COLOR_WHITE));
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF800000');
                
                // Add subtitle row
                $sheet->mergeCells('A2:C2');
                $sheet->setCellValue('A2', 'Generated on: ' . now()->format('F d, Y \a\t g:i A'));
                $sheet->getStyle('A2')->getFont()->setSize(11);
                $sheet->getStyle('A2')->getFont()->setColor(new Color(Color::COLOR_WHITE));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A2')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF800000');
                
                // Update row references after insertion
                $headerRow = 3;
                $dataStartRow = 4;
                
                // Style the header row (now at row 3)
                $sheet->getStyle("A{$headerRow}:C{$headerRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$headerRow}:C{$headerRow}")->getFont()->setSize(12);
                $sheet->getStyle("A{$headerRow}:C{$headerRow}")->getFont()->setColor(new Color(Color::COLOR_WHITE));
                $sheet->getStyle("A{$headerRow}:C{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A{$headerRow}:C{$headerRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A{$headerRow}:C{$headerRow}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF800000');
                
                // Add borders to all cells
                $sheet->getStyle("A1:C{$lastRow}")->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->setColor(new Color(Color::COLOR_BLACK));
                
                // Add alternating row colors for better readability
                for ($row = $dataStartRow; $row <= $lastRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:C{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFF8F9FA'); // Light gray
                    }
                    
                    // Highlight important metrics
                    $metric = $sheet->getCell("A{$row}")->getValue();
                    if (str_contains($metric, 'Total Revenue')) {
                        $sheet->getStyle("B{$row}")->getFont()->setColor(new Color(Color::COLOR_GREEN));
                        $sheet->getStyle("B{$row}")->getFont()->setBold(true);
                    } elseif (str_contains($metric, 'Date Range')) {
                        $sheet->getStyle("B{$row}")->getFont()->setColor(new Color(Color::COLOR_BLUE));
                        $sheet->getStyle("B{$row}")->getFont()->setBold(true);
                    }
                }
                
                // Center align specific columns
                $sheet->getStyle("A{$dataStartRow}:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Metric
                $sheet->getStyle("B{$dataStartRow}:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Value
                $sheet->getStyle("C{$dataStartRow}:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Description
                
                // Wrap text for description column
                $sheet->getStyle("C{$dataStartRow}:C{$lastRow}")->getAlignment()->setWrapText(true);
                
                // Set specific row heights for title and header rows
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(22);
                
                // Auto-adjust row heights
                foreach ($sheet->getRowDimensions() as $rowDimension) {
                    $rowDimension->setRowHeight(-1);
                }
                
                // Set print area
                $sheet->getPageSetup()->setPrintArea("A1:C{$lastRow}");
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            }
        ];
    }
} 