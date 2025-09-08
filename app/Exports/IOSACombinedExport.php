<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
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
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class IOSACombinedExport implements WithMultipleSheets
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

        $sheets[] = new IOSACombinedDataSheet($this->reservations, $this->events);

        if ($this->includeSummary) {
            $sheets[] = new IOSASummarySheetNew($this->reservations, $this->events);
        }

        return $sheets;
    }
}

class IOSACombinedDataSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents
{
    protected $combinedData;

    public function __construct($reservations, $events)
    {
        $this->combinedData = collect();

        foreach ($reservations as $reservation) {
            $this->combinedData->push((object) [
                'type' => 'Reservation',
                'data' => $reservation,
                'start_date' => $reservation->start_date,
                'id' => $reservation->id
            ]);
        }

        foreach ($events as $event) {
            $this->combinedData->push((object) [
                'type' => 'Event',
                'data' => $event,
                'start_date' => $event->start_date,
                'id' => $event->id
            ]);
        }

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
        }
        return $this->mapEvent($item->data);
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
            'N/A'
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

                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->mergeCells('A2:' . $highestColumn . '2');

                $sheet->setCellValue('A1', 'IOSA COMBINED REPORTS & ANALYTICS');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '800000'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                $sheet->setCellValue('A2', 'Reservations and Events - Combined Report');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                $sheet->getStyle('A3:' . $highestColumn . '3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '800000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

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

                $sheet->getStyle('I4:I' . ($highestRow + 2))->getAlignment()->setWrapText(true);
                for ($row = 4; $row <= $highestRow + 2; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }

                $sheet->getStyle('A1:' . $highestColumn . ($highestRow + 2))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);

                $sheet->getPageSetup()->setPrintArea('A1:' . $highestColumn . ($highestRow + 2));
                $sheet->getHeaderFooter()->setOddHeader('&C&HIOSA COMBINED REPORTS & ANALYTICS');
                $sheet->getHeaderFooter()->setOddFooter('&L&B' . $sheet->getTitle() . '&RPage &P of &N');
            }
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 25,
            'C' => 30,
            'D' => 18,
            'E' => 12,
            'F' => 18,
            'G' => 12,
            'H' => 12,
            'I' => 40,
            'J' => 25,
            'K' => 25,
            'L' => 25,
            'M' => 15,
            'N' => 15,
        ];
    }
}

class IOSASummarySheetNew implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents
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
        $summaryData = collect();

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

        $summaryData->push((object) [
            'category' => 'Events',
            'metric' => 'Total Count',
            'value' => $this->events->count(),
            'details' => 'All events in selected period'
        ]);

        $summaryData->push((object) [
            'category' => 'Events',
            'metric' => 'Upcoming Events',
            'value' => $this->events->filter(function($event) { return $event->start_date && now()->isBefore($event->start_date); })->count(),
            'details' => 'Events not yet started'
        ]);

        $summaryData->push((object) [
            'category' => 'Events',
            'metric' => 'Completed Events',
            'value' => $this->events->filter(function($event) { return $event->end_date && now()->isAfter($event->end_date); })->count(),
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

                $sheet->insertNewRowBefore(1, 1);
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->setCellValue('A1', 'IOSA REPORTS SUMMARY');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '800000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

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
            'A' => 15,
            'B' => 20,
            'C' => 20,
            'D' => 40,
        ];
    }
}


