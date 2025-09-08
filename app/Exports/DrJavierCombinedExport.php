<?php

namespace App\Exports;

use App\Models\Reservation;
use App\Models\Event;
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
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Carbon\Carbon;

class DrJavierCombinedExport implements WithMultipleSheets
{
    protected $reservations;
    protected $events;
    protected $includeSummary;

    public function __construct($reservations, $events, $includeSummary = true)
    {
        $this->reservations = $reservations;
        $this->events = $events;
        $this->includeSummary = $includeSummary;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Add main combined data sheet
        $sheets[] = new DrJavierCombinedDataSheet($this->reservations, $this->events);

        // Add summary sheet if requested
        if ($this->includeSummary) {
            $sheets[] = new DrJavierSummarySheet($this->reservations, $this->events);
        }

        return $sheets;
    }
}

class DrJavierCombinedDataSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents
{
    protected $combinedData;

    public function __construct($reservations, $events)
    {
        // Combine and sort by start date
        $this->combinedData = collect();
        
        // Add reservations with type indicator
        foreach ($reservations as $reservation) {
            $this->combinedData->push((object) [
                'type' => 'Reservation',
                'data' => $reservation,
                'start_date' => $reservation->start_date,
                'id' => $reservation->id
            ]);
        }
        
        // Add events with type indicator
        foreach ($events as $event) {
            $this->combinedData->push((object) [
                'type' => 'Event',
                'data' => $event,
                'start_date' => $event->start_date,
                'id' => $event->id
            ]);
        }
        
        // Sort by start date ascending
        $this->combinedData = $this->combinedData->sortBy('start_date');
    }

    public function collection()
    {
        return $this->combinedData;
    }

    public function headings(): array
    {
        return [
            'Type',
            'ID',
            'Title',
            'Start Date',
            'Start Time',
            'End Date',
            'End Time',
            'Duration (Hours)',
            'Description',
            'Venue Name',
            'Requester/Organizer',
            'Department',
            'Status',
            'Financial Info'
        ];
    }

    public function map($item): array
    {
        if ($item->type === 'Reservation') {
            return $this->mapReservation($item->data);
        } else {
            return $this->mapEvent($item->data);
        }
    }

    private function mapReservation($reservation): array
    {
        $startDate = $reservation->start_date;
        $endDate = $reservation->end_date;
        $duration = $startDate && $endDate ? $startDate->diffInHours($endDate) : 0;

        return [
            'Reservation',
            $reservation->reservation_id ?? $reservation->id,
            $reservation->event_title ?? 'N/A',
            $startDate ? $startDate->format('F j, Y') : 'N/A',
            $startDate ? $startDate->format('g:i A') : 'N/A',
            $endDate ? $endDate->format('F j, Y') : 'N/A',
            $endDate ? $endDate->format('g:i A') : 'N/A',
            $duration,
            $reservation->purpose ?? 'N/A',
            optional($reservation->venue)->name ?? 'N/A',
            optional($reservation->user)->name ?? 'N/A',
            $reservation->department ?? 'N/A',
            ucfirst(str_replace('_', ' ', $reservation->status)),
            $reservation->final_price ? '₱' . number_format($reservation->final_price, 2) : 'N/A'
        ];
    }

    private function mapEvent($event): array
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
            'Event',
            $event->event_id ?? $event->id,
            $event->title ?? 'N/A',
            $startDate ? $startDate->format('F j, Y') : 'N/A',
            $startDate ? $startDate->format('g:i A') : 'N/A',
            $endDate ? $endDate->format('F j, Y') : 'N/A',
            $endDate ? $endDate->format('g:i A') : 'N/A',
            $duration,
            $event->description ?? 'N/A',
            optional($event->venue)->name ?? 'N/A',
            $event->organizer ?? 'N/A',
            $event->department ?? 'N/A',
            $status,
            'N/A' // Events don't have financial info
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
                $sheet->setCellValue('A1', 'DR. JAVIER COMBINED REPORTS & ANALYTICS');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '800000'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                $sheet->setCellValue('A2', 'Reservations and Events - Combined Report');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Apply font and center alignment to all cells
                $sheet->getStyle('A1:' . $highestColumn . ($highestRow + 2))->getFont()->setName('Arial');
                $sheet->getStyle('A1:' . $highestColumn . ($highestRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:' . $highestColumn . ($highestRow + 2))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                
                // Style the header row
                $sheet->getStyle('A3:' . $highestColumn . '3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '800000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Style the data rows
                for ($row = 4; $row <= $highestRow + 2; $row++) {
                    $typeCell = $sheet->getCell('A' . $row)->getValue();
                    if ($typeCell === 'Reservation') {
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
                            'font' => ['name' => 'Arial', 'size' => 10]
                        ]);
                    } elseif ($typeCell === 'Event') {
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E8']],
                            'font' => ['name' => 'Arial', 'size' => 10]
                        ]);
                    }
                }

                // Enable text wrapping for description column (I)
                $sheet->getStyle('I4:I' . ($highestRow + 2))->getAlignment()->setWrapText(true);
                
                // Set row height for description column to accommodate wrapped text
                for ($row = 4; $row <= $highestRow + 2; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(-1); // Auto-fit height
                }

                // Add borders to all cells with reduced opacity
                $sheet->getStyle('A1:' . $highestColumn . ($highestRow + 2))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);

                // Set print area and header/footer
                $sheet->getPageSetup()->setPrintArea('A1:' . $highestColumn . ($highestRow + 2));
                $sheet->getHeaderFooter()->setOddHeader('&C&HDR. JAVIER COMBINED REPORTS & ANALYTICS');
                $sheet->getHeaderFooter()->setOddFooter('&L&B' . $sheet->getTitle() . '&RPage &P of &N');
            }
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Type
            'B' => 25,  // ID
            'C' => 30,  // Title
            'D' => 18,  // Start Date
            'E' => 12,  // Start Time
            'F' => 18,  // End Date
            'G' => 12,  // End Time
            'H' => 12,  // Duration
            'I' => 40,  // Description
            'J' => 25,  // Venue Name
            'K' => 25,  // Requester/Organizer
            'L' => 25,  // Department
            'M' => 15,  // Status
            'N' => 15,  // Financial Info
        ];
    }
}

class DrJavierSummarySheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents
{
    protected $reservations;
    protected $events;

    public function __construct($reservations, $events)
    {
        $this->reservations = $reservations;
        $this->events = $events;
    }

    public function collection()
    {
        // Create summary data
        $summaryData = collect();
        
        // Reservations summary
        $summaryData->push((object) [
            'category' => 'Reservations',
            'metric' => 'Total Count',
            'value' => $this->reservations->count(),
            'details' => 'All reservations in selected period'
        ]);
        
        $summaryData->push((object) [
            'category' => 'Reservations',
            'metric' => 'Total Revenue',
            'value' => '₱' . number_format($this->reservations->where('status', 'completed')->sum('final_price'), 2),
            'details' => 'From completed reservations only'
        ]);
        
        $summaryData->push((object) [
            'category' => 'Reservations',
            'metric' => 'Average Revenue',
            'value' => '₱' . number_format($this->reservations->where('status', 'completed')->avg('final_price'), 2),
            'details' => 'Per completed reservation'
        ]);
        
        // Events summary
        $summaryData->push((object) [
            'category' => 'Events',
            'metric' => 'Total Count',
            'value' => $this->events->count(),
            'details' => 'All events in selected period'
        ]);
        
        $summaryData->push((object) [
            'category' => 'Events',
            'metric' => 'Upcoming Events',
            'value' => $this->events->filter(function($event) {
                return $event->start_date && now()->isBefore($event->start_date);
            })->count(),
            'details' => 'Events not yet started'
        ]);
        
        $summaryData->push((object) [
            'category' => 'Events',
            'metric' => 'Completed Events',
            'value' => $this->events->filter(function($event) {
                return $event->end_date && now()->isAfter($event->end_date);
            })->count(),
            'details' => 'Events that have finished'
        ]);
        
        return $summaryData;
    }

    public function headings(): array
    {
        return [
            'Category',
            'Metric',
            'Value',
            'Details'
        ];
    }

    public function map($item): array
    {
        return [
            $item->category,
            $item->metric,
            $item->value,
            $item->details
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

                // Add title
                $sheet->insertNewRowBefore(1, 1);
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->setCellValue('A1', 'DR. JAVIER REPORTS SUMMARY');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '800000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Add borders
                $sheet->getStyle('A2:' . $highestColumn . ($highestRow + 1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);
            }
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Category
            'B' => 20,  // Metric
            'C' => 20,  // Value
            'D' => 40,  // Details
        ];
    }
}
