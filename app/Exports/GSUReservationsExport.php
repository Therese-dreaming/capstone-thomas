<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GSUReservationsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents, WithColumnFormatting
{
    protected $reservations;

    public function __construct($reservations)
    {
        $this->reservations = $reservations;
    }

    public function collection()
    {
        return $this->reservations;
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
            'Final Price'
        ];
    }

    public function map($reservation): array
    {
        $startDate = $reservation->start_date;
        $endDate = $reservation->end_date;
        $duration = $startDate && $endDate ? $startDate->diffInHours($endDate) : 0;

        return [
            $reservation->reservation_id ?? $reservation->id,
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
            $reservation->final_price ? '₱' . number_format($reservation->final_price, 2) : '₱0.00'
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

                $sheet->setCellValue('A1', 'GSU RESERVATION REPORTS & ANALYTICS');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '800000'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                $sheet->setCellValue('A2', 'Comprehensive Reservation Reports - Generated on ' . now()->format('F d, Y \a\t g:i A'));
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

    public function columnFormats(): array
    {
        return [
            'P' => '#,##0.00',
            'Q' => '0%',
            'R' => '#,##0.00',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
            'C' => 30,
            'D' => 20,
            'E' => 30,
            'F' => 20,
            'G' => 30,
            'H' => 15,
            'I' => 18,
            'J' => 10,
            'K' => 18,
            'L' => 10,
            'M' => 15,
            'N' => 12,
            'O' => 18,
            'P' => 15,
            'Q' => 15,
            'R' => 15
        ];
    }
}


