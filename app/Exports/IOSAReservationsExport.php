<?php

namespace App\Exports;

use App\Models\Reservation;
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

class IOSAReservationsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents, WithColumnFormatting, WithMultipleSheets
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = is_array($filters) ? $filters : [];
    }

    public function collection()
    {
        $query = Reservation::with(['user', 'venue']);

        // Apply filters
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('start_date', '>=', $this->filters['start_date']);
        }
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('end_date', '<=', $this->filters['end_date']);
        }
        
        // Handle status filtering - export statuses take priority
        if (!empty($this->filters['export_statuses'])) {
            $exportStatuses = is_array($this->filters['export_statuses']) 
                ? $this->filters['export_statuses'] 
                : [$this->filters['export_statuses']];
            $query->whereIn('status', $exportStatuses);
        } elseif (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        
        if (!empty($this->filters['venue_id'])) {
            $query->where('venue_id', $this->filters['venue_id']);
        }
        if (!empty($this->filters['department'])) {
            $query->where('department', $this->filters['department']);
        }

        // If no filters are applied, get all reservations
        // This ensures we always have data to export
        $results = $query->orderBy('start_date', 'asc')->get();
        
        // Log for debugging (remove in production)
        \Log::info('IOSA Export - Filters applied: ', $this->filters);
        \Log::info('IOSA Export - Results count: ' . $results->count());
        
        // If no results found, log the query for debugging
        if ($results->count() === 0) {
            \Log::warning('IOSA Export - No results found with filters: ', $this->filters);
            \Log::info('IOSA Export - Total reservations in DB: ' . Reservation::count());
        }
        
        return $results;
    }

    public function headings(): array
    {
        return [
            'Reservation ID',
            'Event Title',
            'Purpose',
            'Requester Name',
            'Requester Email',
            'Department',
            'Venue Name',
            'Venue Capacity',
            'Start Date',
            'Start Time',
            'End Date',
            'End Time',
            'Duration (Hours)',
            'Capacity',
            'Status',
            'Base Price',
            'Discount (%)',
            'Final Price',
            'Created Date',
            'Last Updated'
        ];
    }

    public function map($reservation): array
    {
        $startDate = $reservation->start_date;
        $endDate = $reservation->end_date;
        $duration = $startDate && $endDate ? $startDate->diffInHours($endDate) : 0;

        return [
            $reservation->id,
            $reservation->event_title ?? 'N/A',
            $reservation->purpose ?? 'N/A',
            optional($reservation->user)->name ?? 'N/A',
            optional($reservation->user)->email ?? 'N/A',
            $reservation->department ?? 'N/A',
            optional($reservation->venue)->name ?? 'N/A',
            optional($reservation->venue)->capacity ?? 'N/A',
            $startDate ? $startDate->format('F j, Y') : 'N/A',
            $startDate ? $startDate->format('g:i A') : 'N/A',
            $endDate ? $endDate->format('F j, Y') : 'N/A',
            $endDate ? $endDate->format('g:i A') : 'N/A',
            $duration,
            $reservation->capacity ?? 'N/A',
            str_replace('_', ' ', $reservation->status),
            $reservation->base_price ? number_format($reservation->base_price, 2) : '₱0.00',
            $reservation->discount_percentage ? $reservation->discount_percentage . '%' : '0%',
            $reservation->final_price ? '₱' . number_format($reservation->final_price, 2) : '₱0.00',
            $reservation->created_at ? $reservation->created_at->format('F j, Y g:i A') : 'N/A',
            $reservation->updated_at ? $reservation->updated_at->format('F j, Y g:i A') : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '800000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Add title and subtitle rows
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->mergeCells('A2:' . $highestColumn . '2');

                // Title styling
                $sheet->setCellValue('A1', 'IOSA RESERVATION REPORTS & ANALYTICS');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '800000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Subtitle styling
                $sheet->setCellValue('A2', 'Comprehensive Reservation Reports - Generated on ' . now()->format('F d, Y \a\t g:i A'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 12, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Header row styling (now row 3)
                $sheet->getStyle('A3:' . $highestColumn . '3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '800000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                    ]
                ]);

                // Data rows styling
                $sheet->getStyle('A4:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]
                    ]
                ]);

                // Alternating row colors
                for ($row = 4; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']]
                        ]);
                    }
                }

                // Conditional formatting for high-value reservations
                $finalPriceColumn = 'R';
                for ($row = 4; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell($finalPriceColumn . $row)->getValue();
                    if (is_string($cellValue) && strpos($cellValue, '₱') !== false) {
                        $price = (float) str_replace(['₱', ','], '', $cellValue);
                        if ($price > 1000) {
                            $sheet->getStyle($finalPriceColumn . $row)->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D4EDDA']],
                                'font' => ['bold' => true, 'color' => ['rgb' => '155724']]
                            ]);
                        }
                    }
                }

                // Conditional formatting for long-duration events
                $durationColumn = 'M';
                for ($row = 4; $row <= $highestRow; $row++) {
                    $duration = $sheet->getCell($durationColumn . $row)->getValue();
                    if (is_numeric($duration) && $duration > 8) {
                        $sheet->getStyle($durationColumn . $row)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3CD']],
                            'font' => ['bold' => true, 'color' => ['rgb' => '856404']]
                        ]);
                    }
                }

                // Set specific column alignments
                $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I:J')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K:L')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('M:N')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('O:O')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('P:R')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('S:T')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Enable text wrapping for long content
                $sheet->getStyle('B:C')->getAlignment()->setWrapText(true);

                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(20);

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
            'P' => '#,##0.00',  // Base Price
            'Q' => '0%',        // Discount
            'R' => '#,##0.00',  // Final Price
        ];
    }


    public function sheets(): array
    {
        return [
            'Reports' => $this,
            'Summary' => new IOSASummarySheet($this->filters)
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Reservation ID
            'B' => 30,  // Event Title
            'C' => 30,  // Purpose
            'D' => 20,  // Requester Name
            'E' => 30,  // Requester Email
            'F' => 20,  // Department
            'G' => 30,  // Venue Name
            'H' => 15,  // Venue Capacity
            'I' => 18,  // Start Date
            'J' => 10,  // Start Time
            'K' => 18,  // End Date
            'L' => 10,  // End Time
            'M' => 15,  // Duration
            'N' => 12,  // Capacity
            'O' => 18,  // Status
            'P' => 15,  // Base Price
            'Q' => 15,  // Discount
            'R' => 15,  // Final Price
            'S' => 25,  // Created Date
            'T' => 25   // Last Updated
        ];
    }
}
