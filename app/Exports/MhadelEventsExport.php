<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class MhadelEventsExport implements WithMultipleSheets
{
    protected $events;

    public function __construct($events)
    {
        $this->events = $events;
    }

    public function sheets(): array
    {
        return [
            new MhadelEventsDataSheet($this->events),
            new MhadelEventsSummarySheet($this->events),
        ];
    }

}

// Data Sheet Class
class MhadelEventsDataSheet implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles, WithEvents, WithTitle
{
    protected $events;

    public function __construct($events)
    {
        $this->events = $events;
    }

    public function title(): string
    {
        return 'Events Data';
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
            'Organizer',
            'Department',
            'Start Date',
            'Start Time',
            'End Date',
            'End Time',
            'Duration (Hours)',
            'Venue Name',
            'Venue Capacity',
            'Max Participants',
            'Equipment Count',
            'Status',
            'Created Date',
            'Description'
        ];
    }

    public function map($event): array
    {
        $startDate = $event->start_date;
        $endDate = $event->end_date;
        $duration = $startDate && $endDate ? $startDate->diffInHours($endDate) : 0;

        // Get equipment count
        $equipmentCount = 0;
        if ($event->equipment_details && is_array($event->equipment_details)) {
            $equipmentCount = count($event->equipment_details);
        }

        // Format status with proper capitalization
        $status = ucfirst($event->status ?? 'Unknown');
        if ($status === 'Pending_venue') {
            $status = 'Pending Venue';
        }

        // Limit description length for better readability
        $description = $event->description ?? 'No description provided';
        if (strlen($description) > 100) {
            $description = substr($description, 0, 97) . '...';
        }

        return [
            $event->event_id ?? 'EVT-' . str_pad($event->id, 4, '0', STR_PAD_LEFT),
            $event->title ?? 'Untitled Event',
            $event->organizer ?? 'N/A',
            $event->department ?? 'N/A',
            $startDate ? $startDate->format('M j, Y') : 'Not Set',
            $startDate ? $startDate->format('g:i A') : 'Not Set',
            $endDate ? $endDate->format('M j, Y') : 'Not Set',
            $endDate ? $endDate->format('g:i A') : 'Not Set',
            $duration . ' hrs',
            optional($event->venue)->name ?? 'No Venue Assigned',
            optional($event->venue)->capacity ?? 'N/A',
            $event->max_participants ?? 'Not Specified',
            $equipmentCount > 0 ? $equipmentCount . ' items' : 'None',
            $status,
            $event->created_at ? $event->created_at->format('M j, Y') : 'N/A',
            $description
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,  // Event ID
            'B' => 35,  // Event Title
            'C' => 25,  // Organizer
            'D' => 20,  // Department
            'E' => 18,  // Start Date
            'F' => 15,  // Start Time
            'G' => 18,  // End Date
            'H' => 15,  // End Time
            'I' => 15,  // Duration
            'J' => 30,  // Venue Name
            'K' => 15,  // Venue Capacity
            'L' => 18,  // Max Participants
            'M' => 18,  // Equipment Count
            'N' => 18,  // Status
            'O' => 18,  // Created Date
            'P' => 50   // Description
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Insert 3 rows at the top for title and subtitle
                $sheet->insertNewRowBefore(1, 3);
                
                // Set custom row heights for better spacing
                $sheet->getRowDimension(1)->setRowHeight(35); // Title row
                $sheet->getRowDimension(2)->setRowHeight(25); // Subtitle row
                $sheet->getRowDimension(3)->setRowHeight(15); // Spacer row
                $sheet->getRowDimension(4)->setRowHeight(35); // Header row
                
                // Set all data rows to have consistent height
                for ($row = 5; $row <= $highestRow + 3; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(30);
                }

                // Merge cells for title and subtitle
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->mergeCells('A2:' . $highestColumn . '2');
                $sheet->mergeCells('A3:' . $highestColumn . '3');

                // Set title
                $sheet->setCellValue('A1', 'MS. MHADEL EVENTS MANAGEMENT SYSTEM - DATA');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true, 
                        'size' => 18, 
                        'color' => ['rgb' => '800000'], 
                        'name' => 'Calibri'
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER, 
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID, 
                        'startColor' => ['rgb' => 'F8F9FA']
                    ]
                ]);

                // Set subtitle with current date and time
                $sheet->setCellValue('A2', 'Events Data Export - Generated on ' . now()->format('F d, Y \a\t g:i A'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'size' => 12, 
                        'color' => ['rgb' => '6C757D'], 
                        'name' => 'Calibri',
                        'italic' => true
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER, 
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID, 
                        'startColor' => ['rgb' => 'F8F9FA']
                    ]
                ]);

                // Style the spacer row
                $sheet->getStyle('A3:' . $highestColumn . '3')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID, 
                        'startColor' => ['rgb' => 'FFFFFF']
                    ]
                ]);

                // Style header row with enhanced design
                $sheet->getStyle('A4:' . $highestColumn . '4')->applyFromArray([
                    'font' => [
                        'bold' => true, 
                        'size' => 12, 
                        'color' => ['rgb' => 'FFFFFF'], 
                        'name' => 'Calibri'
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID, 
                        'startColor' => ['rgb' => '800000']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER, 
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM, 
                            'color' => ['rgb' => '800000']
                        ]
                    ]
                ]);

                // Style data rows with alternating colors and proper alignment
                for ($row = 5; $row <= $highestRow + 3; $row++) {
                    $fillColor = ($row % 2 == 0) ? 'FFFFFF' : 'F8F9FA'; // Alternating row colors
                    
                    $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                        'font' => [
                            'name' => 'Calibri', 
                            'size' => 11,
                            'color' => ['rgb' => '212529']
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID, 
                            'startColor' => ['rgb' => $fillColor]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER, 
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN, 
                                'color' => ['rgb' => 'DEE2E6']
                            ]
                        ]
                    ]);
                }

                // Add special styling for status column (column N - 14th column)
                $statusColumn = 'N';
                for ($row = 5; $row <= $highestRow + 3; $row++) {
                    $cellValue = $sheet->getCell($statusColumn . $row)->getValue();
                    $statusColor = '6C757D'; // Default gray
                    
                    switch (strtolower($cellValue)) {
                        case 'upcoming':
                            $statusColor = '0D6EFD'; // Blue
                            break;
                        case 'ongoing':
                            $statusColor = 'FD7E14'; // Orange
                            break;
                        case 'completed':
                            $statusColor = '198754'; // Green
                            break;
                        case 'cancelled':
                            $statusColor = 'DC3545'; // Red
                            break;
                        case 'pending venue':
                            $statusColor = '6F42C1'; // Purple
                            break;
                    }
                    
                    $sheet->getStyle($statusColumn . $row)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => $statusColor]
                        ]
                    ]);
                }

                // Add border around the entire data area
                $sheet->getStyle('A1:' . $highestColumn . ($highestRow + 3))->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK, 
                            'color' => ['rgb' => '800000']
                        ]
                    ]
                ]);
            }
        ];
    }
}

// Summary Sheet Class
class MhadelEventsSummarySheet implements FromCollection, WithHeadings, WithColumnWidths, WithStyles, WithEvents, WithTitle
{
    protected $events;

    public function __construct($events)
    {
        $this->events = $events;
    }

    public function title(): string
    {
        return 'Summary Report';
    }

    public function collection()
    {
        // Calculate statistics
        $total = $this->events->count();
        $upcoming = $this->events->where('status', 'upcoming')->count();
        $ongoing = $this->events->where('status', 'ongoing')->count();
        $completed = $this->events->where('status', 'completed')->count();
        $cancelled = $this->events->where('status', 'cancelled')->count();
        $pendingVenue = $this->events->where('status', 'pending_venue')->count();

        // Department breakdown
        $departmentStats = $this->events->groupBy('department')->map(function ($events, $dept) {
            return $events->count();
        })->sortDesc();

        // Venue usage
        $venueStats = $this->events->filter(function($event) {
            return $event->venue_id !== null;
        })->groupBy('venue.name')->map(function ($events, $venue) {
            return $events->count();
        })->sortDesc();

        // Monthly breakdown
        $monthlyStats = $this->events->groupBy(function($event) {
            return $event->created_at ? $event->created_at->format('Y-m') : 'Unknown';
        })->map(function ($events, $month) {
            return $events->count();
        })->sortKeys();

        // Equipment usage
        $totalEquipmentRequests = $this->events->filter(function($event) {
            return $event->equipment_details && is_array($event->equipment_details) && count($event->equipment_details) > 0;
        })->count();

        // Average duration
        $eventsWithDuration = $this->events->filter(function($event) {
            return $event->start_date && $event->end_date;
        });
        $avgDuration = $eventsWithDuration->count() > 0 ? 
            $eventsWithDuration->avg(function($event) {
                return $event->start_date->diffInHours($event->end_date);
            }) : 0;

        // Build summary data
        $summaryData = collect([
            ['Metric', 'Value', 'Percentage'],
            ['', '', ''], // Spacer
            ['OVERALL STATISTICS', '', ''],
            ['Total Events', $total, '100%'],
            ['Upcoming Events', $upcoming, $total > 0 ? round(($upcoming / $total) * 100, 1) . '%' : '0%'],
            ['Ongoing Events', $ongoing, $total > 0 ? round(($ongoing / $total) * 100, 1) . '%' : '0%'],
            ['Completed Events', $completed, $total > 0 ? round(($completed / $total) * 100, 1) . '%' : '0%'],
            ['Cancelled Events', $cancelled, $total > 0 ? round(($cancelled / $total) * 100, 1) . '%' : '0%'],
            ['Pending Venue Assignment', $pendingVenue, $total > 0 ? round(($pendingVenue / $total) * 100, 1) . '%' : '0%'],
            ['', '', ''], // Spacer
            ['EVENT ANALYTICS', '', ''],
            ['Events with Equipment', $totalEquipmentRequests, $total > 0 ? round(($totalEquipmentRequests / $total) * 100, 1) . '%' : '0%'],
            ['Average Event Duration', round($avgDuration, 1) . ' hours', ''],
            ['Events with Assigned Venues', $this->events->whereNotNull('venue_id')->count(), $total > 0 ? round(($this->events->whereNotNull('venue_id')->count() / $total) * 100, 1) . '%' : '0%'],
            ['', '', ''], // Spacer
            ['TOP DEPARTMENTS', '', ''],
        ]);

        // Add department stats
        foreach ($departmentStats->take(5) as $dept => $count) {
            $summaryData->push([
                $dept ?: 'Not Specified', 
                $count, 
                $total > 0 ? round(($count / $total) * 100, 1) . '%' : '0%'
            ]);
        }

        $summaryData->push(['', '', '']); // Spacer
        $summaryData->push(['TOP VENUES', '', '']);

        // Add venue stats
        foreach ($venueStats->take(5) as $venue => $count) {
            $summaryData->push([
                $venue ?: 'Not Specified', 
                $count, 
                $total > 0 ? round(($count / $total) * 100, 1) . '%' : '0%'
            ]);
        }

        $summaryData->push(['', '', '']); // Spacer
        $summaryData->push(['MONTHLY BREAKDOWN', '', '']);

        // Add monthly stats
        foreach ($monthlyStats as $month => $count) {
            $monthName = $month !== 'Unknown' ? \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') : 'Unknown';
            $summaryData->push([
                $monthName, 
                $count, 
                $total > 0 ? round(($count / $total) * 100, 1) . '%' : '0%'
            ]);
        }

        return $summaryData;
    }

    public function headings(): array
    {
        return []; // No headings as we'll handle them in the data
    }

    public function columnWidths(): array
    {
        return [
            'A' => 45,  // Metric - increased width
            'B' => 25,  // Value - increased width
            'C' => 20,  // Percentage - increased width
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                // Insert title rows
                $sheet->insertNewRowBefore(1, 3);
                
                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(40);
                $sheet->getRowDimension(2)->setRowHeight(25);
                $sheet->getRowDimension(3)->setRowHeight(15);
                
                // Set default row height for data
                for ($row = 4; $row <= $highestRow + 3; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(25);
                }

                // Merge cells for title
                $sheet->mergeCells('A1:C1');
                $sheet->mergeCells('A2:C2');
                $sheet->mergeCells('A3:C3');

                // Set title
                $sheet->setCellValue('A1', 'MHADEL EVENTS - SUMMARY REPORT');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true, 
                        'size' => 18, 
                        'color' => ['rgb' => '800000'], 
                        'name' => 'Calibri'
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER, 
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID, 
                        'startColor' => ['rgb' => 'E8F4FD']
                    ]
                ]);

                // Set subtitle
                $sheet->setCellValue('A2', 'Analytics & Statistics Report - Generated on ' . now()->format('F d, Y \a\t g:i A'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'size' => 12, 
                        'color' => ['rgb' => '6C757D'], 
                        'name' => 'Calibri',
                        'italic' => true
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER, 
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID, 
                        'startColor' => ['rgb' => 'E8F4FD']
                    ]
                ]);

                // Style all data rows
                for ($row = 4; $row <= $highestRow + 3; $row++) {
                    $cellValueA = $sheet->getCell('A' . $row)->getValue();
                    
                    // Check if this is a section header
                    if (in_array($cellValueA, ['OVERALL STATISTICS', 'EVENT ANALYTICS', 'TOP DEPARTMENTS', 'TOP VENUES', 'MONTHLY BREAKDOWN'])) {
                        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                            'font' => [
                                'bold' => true, 
                                'size' => 14, 
                                'color' => ['rgb' => 'FFFFFF'], 
                                'name' => 'Calibri'
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID, 
                                'startColor' => ['rgb' => '800000']
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER, 
                                'vertical' => Alignment::VERTICAL_CENTER
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_MEDIUM, 
                                    'color' => ['rgb' => '800000']
                                ]
                            ]
                        ]);
                    } elseif ($cellValueA === '' || $cellValueA === null) {
                        // Spacer rows
                        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID, 
                                'startColor' => ['rgb' => 'FFFFFF']
                            ]
                        ]);
                    } else {
                        // Data rows
                        $fillColor = ($row % 2 == 0) ? 'F8F9FA' : 'FFFFFF';
                        
                        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                            'font' => [
                                'name' => 'Calibri', 
                                'size' => 11,
                                'color' => ['rgb' => '212529']
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID, 
                                'startColor' => ['rgb' => $fillColor]
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER, 
                                'vertical' => Alignment::VERTICAL_CENTER
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN, 
                                    'color' => ['rgb' => 'DEE2E6']
                                ]
                            ]
                        ]);

                        // Make metric names left-aligned
                        $sheet->getStyle('A' . $row)->applyFromArray([
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_LEFT, 
                                'vertical' => Alignment::VERTICAL_CENTER
                            ],
                            'font' => [
                                'bold' => true
                            ]
                        ]);
                    }
                }

                // Add border around entire summary
                $sheet->getStyle('A1:C' . ($highestRow + 3))->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK, 
                            'color' => ['rgb' => '800000']
                        ]
                    ]
                ]);
            }
        ];
    }
}
