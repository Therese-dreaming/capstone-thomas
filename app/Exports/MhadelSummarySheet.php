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
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class MhadelSummarySheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
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
            $query->whereIn('status', $this->filters['export_statuses']);
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
        $totalRevenue = $reservations->sum('final_price');
        $totalDuration = $reservations->sum(function($r) {
            if ($r->start_date && $r->end_date) {
                return $r->start_date->diffInHours($r->end_date);
            }
            return 0;
        });

        $statusCounts = $reservations->groupBy('status')->map->count();
        $venueStats = $reservations->groupBy('venue.name')->map(function($group) {
            return [
                'count' => $group->count(),
                'revenue' => $group->sum('final_price'),
                'capacity' => $group->sum('capacity')
            ];
        });

        $departmentStats = $reservations->groupBy('department')->map(function($group) {
            return [
                'count' => $group->count(),
                'revenue' => $group->sum('final_price')
            ];
        });

        $dateRange = '';
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $startDate = \Carbon\Carbon::parse($this->filters['start_date'])->format('F j, Y');
            $endDate = \Carbon\Carbon::parse($this->filters['end_date'])->format('F j, Y');
            $dateRange = $startDate . ' to ' . $endDate;
        } else {
            $dateRange = 'All time';
        }

        return collect([
            ['Total Reservations', $totalReservations, 'Total number of reservations in the system'],
            ['Total Revenue', '₱' . number_format($totalRevenue, 2), 'Sum of all final prices'],
            ['Total Duration', $totalDuration . ' hours', 'Combined duration of all events'],
            ['Date Range', $dateRange, 'Filtered date range for this report'],
            ['', '', ''],
            ['Status Breakdown', '', ''],
            ['Pending', $statusCounts['pending'] ?? 0, 'Reservations awaiting approval'],
            ['IOSA Approved', $statusCounts['approved_IOSA'] ?? 0, 'Reservations approved by IOSA'],
            ['Mhadel Approved', $statusCounts['approved_mhadel'] ?? 0, 'Reservations approved by Ms. Mhadel'],
            ['OTP Approved', $statusCounts['approved_OTP'] ?? 0, 'Reservations with final approval'],
            ['Rejected', ($statusCounts['rejected_mhadel'] ?? 0) + ($statusCounts['rejected_OTP'] ?? 0), 'Total rejected reservations'],
            ['', '', ''],
            ['Venue Statistics', '', ''],
        ])->merge(
            $venueStats->map(function($stats, $venueName) {
                return [
                    $venueName,
                    $stats['count'] . ' reservations, ₱' . number_format($stats['revenue'], 2),
                    'Capacity: ' . $stats['capacity'] . ' people'
                ];
            })
        )->merge(collect([
            ['', '', ''],
            ['Department Statistics', '', ''],
        ])->merge(
            $departmentStats->map(function($stats, $dept) {
                return [
                    $dept,
                    $stats['count'] . ' reservations',
                    'Revenue: ₱' . number_format($stats['revenue'], 2)
                ];
            })
        ));
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
        return $row;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 30,
            'C' => 40
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
                $sheet->setCellValue('A1', 'MS. MHADEL REPORTS SUMMARY');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '800000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Subtitle styling
                $sheet->setCellValue('A2', 'Summary Statistics and Analytics - Generated on ' . now()->format('F d, Y \a\t g:i A'));
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

                // Highlight key metrics
                $keyMetrics = [1, 2, 3, 4]; // Total Reservations, Total Revenue, Total Duration, Date Range
                foreach ($keyMetrics as $row) {
                    $actualRow = $row + 3; // Account for inserted title rows
                    $sheet->getStyle('A' . $actualRow . ':' . $highestColumn . $actualRow)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
                        'font' => ['bold' => true, 'color' => ['rgb' => '1976D2']]
                    ]);
                }

                // Highlight section headers
                $sectionHeaders = [6, 12, 18]; // Status Breakdown, Venue Statistics, Department Statistics
                foreach ($sectionHeaders as $row) {
                    $actualRow = $row + 3; // Account for inserted title rows
                    $sheet->getStyle('A' . $actualRow . ':' . $highestColumn . $actualRow)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
                        'font' => ['bold' => true, 'color' => ['rgb' => 'F57C00']]
                    ]);
                }

                // Set column alignments
                $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B:B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('C:C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Enable text wrapping
                $sheet->getStyle('A:C')->getAlignment()->setWrapText(true);

                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(20);

                // Add data validation and protection
                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setPassword('mhadel2024');

                // Set print area and header/footer
                $sheet->getPageSetup()->setPrintArea('A1:' . $highestColumn . $highestRow);
                $sheet->getHeaderFooter()->setOddHeader('&C&HMS. MHADEL REPORTS SUMMARY');
                $sheet->getHeaderFooter()->setOddFooter('&L&B' . $sheet->getTitle() . '&RPage &P of &N');
            }
        ];
    }
} 