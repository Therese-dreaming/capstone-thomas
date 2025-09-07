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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class GSUReservationsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents, WithColumnFormatting, WithMultipleSheets
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
            'Reservation ID',
            'Event Title',
            'Requester Name',
            'Department',
            'Date of Event',
            'Start Time',
            'End Time',
            'Duration (Hours)',
            'Venue Name',
            'Expected Participants',
            'Rate per Hour',
            'Final Price',
            'Equipment Requested',
            'Purpose',
            'Status',
            'Created Date',
            'Last Updated'
        ];
    }

    public function map($r): array
    {
        // Build equipment string
        $equipment = '';
        if (is_array($r->equipment_details)) {
            $parts = [];
            foreach ($r->equipment_details as $item) {
                $name = $item['name'] ?? '';
                $qty = $item['quantity'] ?? '';
                $parts[] = trim($name . ($qty !== '' ? " (Qty: $qty)" : ''));
            }
            $equipment = implode('; ', $parts);
        } elseif (is_string($r->equipment_details)) {
            $equipment = $r->equipment_details;
        }

        // Calculate duration
        $duration = 0;
        if ($r->start_date && $r->end_date) {
            $duration = \Carbon\Carbon::parse($r->start_date)->diffInHours($r->end_date);
        }

        return [
            $r->reservation_id ?? '#' . $r->id,
            $r->event_title ?? 'N/A',
            optional($r->user)->name ?? 'N/A',
            $r->department ?? optional($r->user)->department ?? 'N/A',
            optional($r->start_date)->format('M d, Y') ?? 'N/A',
            optional($r->start_date)->format('g:i A') ?? 'N/A',
            optional($r->end_date)->format('g:i A') ?? 'N/A',
            $duration,
            optional($r->venue)->name ?? 'N/A',
            $r->capacity ?? 'N/A',
            $r->price_per_hour ?? 0,
            $r->final_price ?? 0,
            $equipment ?: 'No equipment requested',
            $r->purpose ?? 'N/A',
            'Final Approved',
            optional($r->created_at)->format('M d, Y g:i A') ?? 'N/A',
            optional($r->updated_at)->format('M d, Y g:i A') ?? 'N/A'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Reservation ID
            'B' => 35,  // Event Title
            'C' => 25,  // Requester Name
            'D' => 20,  // Department
            'E' => 15,  // Date of Event
            'F' => 12,  // Start Time
            'G' => 12,  // End Time
            'H' => 15,  // Duration
            'I' => 25,  // Venue Name
            'J' => 20,  // Expected Participants
            'K' => 15,  // Rate per Hour
            'L' => 15,  // Final Price
            'M' => 40,  // Equipment Requested
            'N' => 50,  // Purpose
            'O' => 15,  // Status
            'P' => 20,  // Created Date
            'Q' => 20,  // Last Updated
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:Q1')->getFont()->setBold(true);
        $sheet->getStyle('A1:Q1')->getFont()->setSize(12);
        $sheet->getStyle('A1:Q1')->getFont()->setColor(new Color(Color::COLOR_WHITE));
        
        // Center align headers
        $sheet->getStyle('A1:Q1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:Q1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                
                // Header background - GSU Maroon color
                $sheet->getStyle('A1:Q1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF800000'); // Maroon color
                
                // Add title row
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:Q1');
                $sheet->setCellValue('A1', 'GSU RESERVATION SYSTEM - FINAL APPROVED RESERVATIONS');
                $sheet->getStyle('A1')->getFont()->setBold(true);
                $sheet->getStyle('A1')->getFont()->setSize(16);
                $sheet->getStyle('A1')->getFont()->setColor(new Color(Color::COLOR_WHITE));
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF800000');
                
                // Add subtitle row
                $sheet->mergeCells('A2:Q2');
                $sheet->setCellValue('A2', 'Generated on: ' . now()->format('F d, Y \a\t g:i A'));
                $sheet->getStyle('A2')->getFont()->setSize(11);
                $sheet->getStyle('A2')->getFont()->setColor(new Color(Color::COLOR_WHITE));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A2')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF800000');
                
                // Update header row references
                $headerRow = 3;
                $dataStartRow = 4;
                
                // Header styling (now at row 3)
                $sheet->getStyle("A{$headerRow}:Q{$headerRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$headerRow}:Q{$headerRow}")->getFont()->setSize(12);
                $sheet->getStyle("A{$headerRow}:Q{$headerRow}")->getFont()->setColor(new Color(Color::COLOR_WHITE));
                $sheet->getStyle("A{$headerRow}:Q{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A{$headerRow}:Q{$headerRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A{$headerRow}:Q{$headerRow}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF800000');
                
                // Add borders to all cells
                $sheet->getStyle("A1:Q{$lastRow}")->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->setColor(new Color(Color::COLOR_BLACK));
                
                // Add alternating row colors for better readability
                for ($row = $dataStartRow; $row <= $lastRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:Q{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFF8F9FA'); // Light gray
                    }
                    
                    // Highlight high-value reservations (over ₱1000)
                    $finalPrice = $sheet->getCell("L{$row}")->getValue();
                    if (is_numeric($finalPrice) && $finalPrice > 1000) {
                        $sheet->getStyle("L{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFFFE6E6'); // Light red
                        $sheet->getStyle("L{$row}")->getFont()->setBold(true);
                    }
                    
                    // Highlight long duration reservations (over 8 hours)
                    $duration = $sheet->getCell("H{$row}")->getValue();
                    if (is_numeric($duration) && $duration > 8) {
                        $sheet->getStyle("H{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFE6F3FF'); // Light blue
                        $sheet->getStyle("H{$row}")->getFont()->setBold(true);
                    }
                }
                
                // Center align specific columns
                $sheet->getStyle("A{$dataStartRow}:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // ID
                $sheet->getStyle("E{$dataStartRow}:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Date
                $sheet->getStyle("F{$dataStartRow}:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Time & Duration
                $sheet->getStyle("J{$dataStartRow}:J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Participants
                $sheet->getStyle("K{$dataStartRow}:L{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Pricing
                $sheet->getStyle("O{$dataStartRow}:O{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status
                $sheet->getStyle("P{$dataStartRow}:Q{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Dates
                
                // Right align pricing columns
                $sheet->getStyle("K{$dataStartRow}:L{$dataStartRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Wrap text for long content columns
                $sheet->getStyle("B{$dataStartRow}:B{$lastRow}")->getAlignment()->setWrapText(true); // Event Title
                $sheet->getStyle("M{$dataStartRow}:M{$lastRow}")->getAlignment()->setWrapText(true); // Equipment
                $sheet->getStyle("N{$dataStartRow}:N{$lastRow}")->getAlignment()->setWrapText(true); // Purpose
                
                // Freeze panes for better navigation
                $sheet->freezePane("A{$dataStartRow}");
                
                // Auto-adjust row heights
                foreach ($sheet->getRowDimensions() as $rowDimension) {
                    $rowDimension->setRowHeight(-1);
                }
                
                // Set specific row heights for title and header rows
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(22);
                
                // Add data validation and protection
                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setSort(true);
                $sheet->getProtection()->setAutoFilter(true);
                
                // Set print area
                $sheet->getPageSetup()->setPrintArea("A1:Q{$lastRow}");
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                
                // Add header and footer for printing
                $sheet->getHeaderFooter()->setOddHeader('&C&H&"Arial,Bold"&16GSU RESERVATION SYSTEM');
                $sheet->getHeaderFooter()->setOddFooter('&L&B' . $sheet->getTitle() . '&RPage &P of &N');
            }
        ];
    }

    public function columnFormats(): array
    {
        return [
            'K' => NumberFormat::FORMAT_NUMBER_00, // Rate per Hour
            'L' => NumberFormat::FORMAT_NUMBER_00, // Final Price
        ];
    }

    public function sheets(): array
    {
        return [
            'Reservations' => $this,
            'Summary' => new GSUSummarySheet($this->rows)
        ];
    }
} 