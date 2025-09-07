<?php

namespace App\Exports;

use App\Models\Reservation;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class IOSASummarySheet implements FromArray, WithHeadings, WithStyles, WithEvents
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = is_array($filters) ? $filters : [];
    }

    public function array(): array
    {
        $query = Reservation::with(['user', 'venue']);

        // Apply same filters as main export
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('start_date', '>=', $this->filters['start_date']);
        }
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('end_date', '<=', $this->filters['end_date']);
        }
        
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

        $reservations = $query->get();

        // Calculate summary statistics
        $totalReservations = $reservations->count();
        $totalRevenue = $reservations->where('status', 'completed')->sum('final_price');
        $averagePrice = $reservations->where('status', 'completed')->avg('final_price') ?? 0;
        
        // Status breakdown
        $statusBreakdown = $reservations->groupBy('status')->map(function($group) {
            return $group->count();
        });
        
        // Department breakdown
        $departmentBreakdown = $reservations->groupBy('department')->map(function($group) {
            return $group->count();
        });
        
        // Venue breakdown
        $venueBreakdown = $reservations->groupBy('venue_id')->map(function($group) {
            return $group->count();
        });
        
        // Monthly breakdown
        $monthlyBreakdown = $reservations->groupBy(function($item) {
            return $item->start_date ? $item->start_date->format('Y-m') : 'Unknown';
        })->map(function($group) {
            return $group->count();
        });

        $summary = [
            ['Metric', 'Value'],
            ['Total Reservations', $totalReservations],
            ['Total Revenue (Completed)', '₱' . number_format($totalRevenue, 2)],
            ['Average Price (Completed)', '₱' . number_format($averagePrice, 2)],
            ['', ''],
            ['Status Breakdown', ''],
        ];

        foreach ($statusBreakdown as $status => $count) {
            $summary[] = [str_replace('_', ' ', $status), $count];
        }

        $summary[] = ['', ''];
        $summary[] = ['Department Breakdown', ''];

        foreach ($departmentBreakdown->take(10) as $department => $count) {
            $summary[] = [$department ?: 'No Department', $count];
        }

        $summary[] = ['', ''];
        $summary[] = ['Venue Usage (Top 10)', ''];

        $venueNames = \App\Models\Venue::whereIn('id', $venueBreakdown->keys())->pluck('name', 'id');
        foreach ($venueBreakdown->sortDesc()->take(10) as $venueId => $count) {
            $venueName = $venueNames[$venueId] ?? 'Unknown Venue';
            $summary[] = [$venueName, $count];
        }

        $summary[] = ['', ''];
        $summary[] = ['Monthly Trends', ''];

        foreach ($monthlyBreakdown->sortKeys() as $month => $count) {
            $summary[] = [$month, $count];
        }

        return $summary;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '800000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            2 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
            ],
            3 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
            ],
            4 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
            ],
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
                $sheet->mergeCells('A1:B1');
                $sheet->setCellValue('A1', 'IOSA RESERVATION REPORTS - SUMMARY');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '800000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']]
                ]);

                // Style section headers
                for ($row = 2; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell('A' . $row)->getValue();
                    if (is_string($cellValue) && strpos($cellValue, 'Breakdown') !== false) {
                        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '800000']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
                            'borders' => [
                                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]
                            ]
                        ]);
                    }
                }

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(40);
                $sheet->getColumnDimension('B')->setWidth(15);

                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(25);
            }
        ];
    }
}
