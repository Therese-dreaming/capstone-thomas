<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class DrJavierEventsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents
{
    protected $events;
    protected $includeSummary;

    public function __construct($events, $includeSummary = true)
    {
        $this->events = $events;
        $this->includeSummary = $includeSummary;
    }

    public function collection()
    {
        return $this->events;
    }

    public function headings(): array
    {
        return [
            'Event ID',
            'Event Title',
            'Start Date',
            'Start Time',
            'End Date',
            'End Time',
            'Duration (Hours)',
            'Description',
            'Venue Name',
            'Max Participants',
            'Event Capacity',
            'Status'
        ];
    }

    public function map($event): array
    {
        $startDate = $event->start_date;
        $endDate = $event->end_date;
        $duration = $startDate && $endDate ? $startDate->diffInHours($endDate) : 0;
        
        // Determine event status based on dates
        $now = now();
        $status = 'Unknown';
        if ($startDate && $endDate) {
            if ($now->isBefore($startDate)) {
                $status = 'Upcoming';
            } elseif ($now->isBetween($startDate, $endDate)) {
                $status = 'Ongoing';
            } else {
                $status = 'Completed';
            }
        }

        return [
            $event->event_id ?? $event->id,
            $event->title ?? 'N/A',
            $startDate ? $startDate->format('F j, Y') : 'N/A',
            $startDate ? $startDate->format('g:i A') : 'N/A',
            $endDate ? $endDate->format('F j, Y') : 'N/A',
            $endDate ? $endDate->format('g:i A') : 'N/A',
            $duration,
            $event->description ?? 'N/A',
            optional($event->venue)->name ?? 'N/A',
            $event->max_participants ?? 'N/A',
            $event->capacity ?? 'N/A',
            $status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // DEBUG: Disable styles method to see if it's interfering
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Determine size before modifications
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Add title and subtitle rows
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->mergeCells('A2:' . $highestColumn . '2');

                // Title styling
                $sheet->setCellValue('A1', 'DR. JAVIER EVENTS REPORTS & ANALYTICS');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '800000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Subtitle styling
                $sheet->setCellValue('A2', 'Events Data Export - Generated on ' . now()->format('F d, Y \a\t g:i A'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 12, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Header row styling (now row 3)
                $sheet->getStyle('A3:' . $highestColumn . '3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '800000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]
                    ]
                ]);

                // Recalculate bounds after inserting rows
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Data rows styling
                $sheet->getStyle('A4:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]
                    ],
                    'font' => ['name' => 'Arial', 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Apply center and middle alignment to all cells
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Enable text wrapping for all data cells to allow content to expand
                $sheet->getStyle('A4:' . $highestColumn . $highestRow)->getAlignment()->setWrapText(true);
                
                // Set a reasonable minimum row height but allow auto-sizing based on content
                for ($row = 4; $row <= $highestRow; $row++) {
                    // Calculate approximate height based on content length
                    $maxContentLength = 0;
                    for ($col = 'A'; $col <= $highestColumn; $col++) {  
                        $cellValue = $sheet->getCell($col . $row)->getValue();
                        $contentLength = strlen((string)$cellValue);
                        $maxContentLength = max($maxContentLength, $contentLength);
                    }
                    
                    // Base height: 20 (minimum) + additional height based on content
                    $rowHeight = 20 + min(30, ceil($maxContentLength / 20) * 5);
                    $sheet->getRowDimension($row)->setRowHeight($rowHeight);
                }

                // Status color coding with proper colors (using ARGB)
                $statusColumn = 'L';
                for ($row = 4; $row <= $highestRow; $row++) {
                    $status = (string) $sheet->getCell($statusColumn . $row)->getValue();
                    $statusLower = strtolower(trim($status));

                    $fillARGB = 'FFFFC107'; // Default Amber
                    $textARGB = 'FF000000'; // Black

                    if ($statusLower === 'upcoming') {
                        $fillARGB = 'FF4CAF50'; // Green
                        $textARGB = 'FFFFFFFF'; // White
                    } elseif ($statusLower === 'ongoing') {
                        $fillARGB = 'FF2196F3'; // Blue
                        $textARGB = 'FFFFFFFF'; // White
                    } elseif ($statusLower === 'completed') {
                        $fillARGB = 'FF9E9E9E'; // Gray
                        $textARGB = 'FFFFFFFF'; // White
                    } elseif ($statusLower === 'cancelled') {
                        $fillARGB = 'FFF44336'; // Red
                        $textARGB = 'FFFFFFFF'; // White
                    }

                    $style = $sheet->getStyle($statusColumn . $row);
                    $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($fillARGB);
                    $style->getFont()->setBold(true)->getColor()->setARGB($textARGB);
                }

                // Conditional formatting for long-duration events
                $durationColumn = 'G';
                for ($row = 4; $row <= $highestRow; $row++) {
                    $duration = $sheet->getCell($durationColumn . $row)->getValue();
                    if (is_numeric($duration) && $duration > 8) {
                        $sheet->getStyle($durationColumn . $row)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3CD']],
                            'font' => ['bold' => true, 'color' => ['rgb' => '856404']]
                        ]);
                    }
                }

                // Alternating row colors (APPLY LAST, but exclude status column)
                for ($row = 4; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        // Apply alternating colors to all columns except status column (L)
                        $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']]
                        ]);
                    }
                }

                // Set row heights for header rows
                $sheet->getRowDimension(1)->setRowHeight(30); // Title row
                $sheet->getRowDimension(2)->setRowHeight(25); // Subtitle row
                $sheet->getRowDimension(3)->setRowHeight(25); // Header row

                // Add data validation and protection
                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setPassword('iosa2024');

                // Set print area and header/footer
                $sheet->getPageSetup()->setPrintArea('A1:' . $highestColumn . $highestRow);
                $sheet->getHeaderFooter()->setOddHeader('&C&HIOSA RESERVATION REPORTS & ANALYTICS');
                $sheet->getHeaderFooter()->setOddFooter('&L&B' . $sheet->getTitle() . '&RPage &P of &N');
            }
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '#,##0',  // Duration (Hours)
        ];
    }



    public function columnWidths(): array
    {
        return [
            'A' => 25,  // Event ID
            'B' => 25,  // Event Title
            'C' => 25,  // Start Date
            'D' => 18,  // Start Time
            'E' => 25,  // End Date
            'F' => 18,  // End Time
            'G' => 18,  // Duration
            'H' => 40,  // Description
            'I' => 30,  // Venue Name
            'J' => 20,  // Max Participants
            'K' => 18,  // Event Capacity
            'L' => 15   // Status
        ];
    }
}
