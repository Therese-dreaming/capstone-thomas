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

class OTPReservationsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents, WithColumnFormatting
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
			'Date Received',
			'Date of Use',
			'Requesting Party',
			'Department/Organization',
			'Purpose',
			'Start Time',
			'End Time',
			'Facilities',
			'No. of Participants',
			'Amount',
			'Equipment Needed',
			'Remarks',
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
				$parts[] = trim($name . ($qty !== '' ? " ($qty)" : ''));
			}
			$equipment = implode('; ', $parts);
		} elseif (is_string($r->equipment_details)) {
			$equipment = $r->equipment_details;
		}

		return [
			optional($r->created_at)->format('Y-m-d'),
			optional($r->start_date)->format('Y-m-d'),
			optional($r->user)->name,
			$r->department ?? optional($r->user)->department,
			$r->purpose,
			optional($r->start_date)->format('g:i A'), // 12-hour format
			optional($r->end_date)->format('g:i A'),   // 12-hour format
			optional($r->venue)->name,
			$r->capacity,
			$r->final_price,
			$equipment,
			$r->notes,
		];
	}

	public function columnWidths(): array
	{
		return [
			'A' => 16, // Date Received
			'B' => 16, // Date of Use
			'C' => 28, // Requesting Party
			'D' => 28, // Department
			'E' => 40, // Purpose
			'F' => 14, // Start Time
			'G' => 14, // End Time
			'H' => 28, // Facilities
			'I' => 18, // Participants
			'J' => 16, // Amount
			'K' => 36, // Equipment
			'L' => 36, // Remarks
		];
	}

	public function styles(Worksheet $sheet)
	{
		// Bold header
		$sheet->getStyle('A1:L1')->getFont()->setBold(true);
		return [];
	}

	public function registerEvents(): array
	{
		return [
			AfterSheet::class => function (AfterSheet $event) {
				$sheet = $event->sheet->getDelegate();
				// Header background
				$sheet->getStyle('A1:L1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
					->getStartColor()->setARGB('FFEFEFEF');
				// Borders
				$lastRow = $sheet->getHighestRow();
				$sheet->getStyle("A1:L{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
				// Wrap text for wide columns
				$sheet->getStyle("E2:E{$lastRow}")->getAlignment()->setWrapText(true);
				$sheet->getStyle("K2:K{$lastRow}")->getAlignment()->setWrapText(true);
				$sheet->getStyle("L2:L{$lastRow}")->getAlignment()->setWrapText(true);
				// Freeze header
				$sheet->freezePane('A2');
			}
		];
	}

	public function columnFormats(): array
	{
		return [
			'J' => NumberFormat::FORMAT_NUMBER_00, // Amount with 2 decimals
		];
	}
} 