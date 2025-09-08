<?php

namespace App\Exports;

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

class MhadelEventsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents
{
    protected $events;

    public function __construct($events)
    {
        $this->events = $events;
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

                $sheet->setCellValue('A1', 'MS. MHADEL EVENTS REPORTS & ANALYTICS');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '800000'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                $sheet->setCellValue('A2', 'Events Data Export - Generated on ' . now()->format('F d, Y \a\t g:i A'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 12, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                $sheet->getStyle('A3:' . $highestColumn . '3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '800000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]
                    ]
                ]);

                $sheet->getStyle('A4:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]
                    ],
                    'font' => ['name' => 'Arial', 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
            }
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 25,
            'D' => 18,
            'E' => 25,
            'F' => 18,
            'G' => 18,
            'H' => 40,
            'I' => 30,
            'J' => 20,
            'K' => 18,
            'L' => 15
        ];
    }
}


