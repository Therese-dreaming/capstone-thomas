<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSU Reservation Receipt</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            line-height: 1.4;
            color: #1f2937;
            background: #ffffff;
            font-size: 10px;
            margin: 0;
            padding: 15px;
        }
        
        .receipt {
            max-width: 400px;
            margin: 0 auto;
            border: 2px solid #000;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .header {
            background: #800000;
            color: white;
            text-align: center;
            padding: 15px 10px;
            border-bottom: 2px solid #000;
        }
        
        .logo {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .subtitle {
            font-size: 11px;
            opacity: 0.9;
        }
        
        .receipt-number {
            background: #f3f4f6;
            padding: 8px;
            text-align: center;
            border-bottom: 1px solid #d1d5db;
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        
        .section {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .section:last-child {
            border-bottom: none;
        }
        
        .section-title {
            font-size: 11px;
            font-weight: 600;
            color: #800000;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }
        
        .info-row:last-child {
            margin-bottom: 0;
        }
        
        .info-label {
            font-size: 9px;
            color: #6b7280;
            font-weight: 500;
        }
        
        .info-value {
            font-size: 10px;
            color: #1f2937;
            font-weight: 600;
            text-align: right;
        }
        
        .divider {
            border-top: 1px dashed #d1d5db;
            margin: 8px 0;
        }
        
        .total-section {
            background: #f8fafc;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }
        
        .total-row:last-child {
            margin-bottom: 0;
        }
        
        .total-label {
            font-size: 10px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .total-value {
            font-size: 12px;
            font-weight: 700;
            color: #800000;
        }
        
        .final-total {
            font-size: 14px;
            font-weight: 700;
            color: #800000;
        }
        
        .currency {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-weight: 600;
        }
        
        .footer {
            text-align: center;
            padding: 15px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer-text {
            font-size: 8px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        
        .status-badge {
            background: #10B981;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }
        
        .equipment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .equipment-item:last-child {
            border-bottom: none;
        }
        
        .equipment-name {
            font-size: 9px;
            color: #1f2937;
        }
        
        .equipment-qty {
            font-size: 9px;
            color: #6b7280;
            font-weight: 500;
        }
        
        @media print {
            body { 
                font-size: 9px; 
                padding: 10px;
            }
            .receipt {
                max-width: 100%;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <div class="logo">GSU RESERVATION SYSTEM</div>
            <div class="subtitle">Final Approved Reservation</div>
        </div>
        
        <!-- Receipt Number -->
        <div class="receipt-number">
            Receipt #{{ $reservation->id }}
        </div>
        
        <!-- Event Details -->
        <div class="section">
            <div class="section-title">Event Details</div>
            <div class="info-row">
                <span class="info-label">Event:</span>
                <span class="info-value">{{ $reservation->event_title ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value">{{ $reservation->start_date->format('M d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Time:</span>
                <span class="info-value">
                    {{ \Carbon\Carbon::parse($reservation->start_date)->format('g:i A') }} - 
                    {{ \Carbon\Carbon::parse($reservation->end_date)->format('g:i A') }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Duration:</span>
                <span class="info-value">
                    {{ \Carbon\Carbon::parse($reservation->start_date)->diffInHours($reservation->end_date) }} hours
                </span>
            </div>
        </div>
        
        <!-- Customer Info -->
        <div class="section">
            <div class="section-title">Customer Information</div>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $reservation->user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Venue:</span>
                <span class="info-value">{{ $reservation->venue->name }}</span>
            </div>
            @if($reservation->capacity)
            <div class="info-row">
                <span class="info-label">Participants:</span>
                <span class="info-value">{{ $reservation->capacity }}</span>
            </div>
            @endif
        </div>
        
        <!-- Equipment -->
        @if($reservation->equipment_details && count($reservation->equipment_details) > 0)
        <div class="section">
            <div class="section-title">Equipment Requested</div>
            @foreach($reservation->equipment_details as $eq)
            <div class="equipment-item">
                <span class="equipment-name">{{ $eq['name'] }}</span>
                <span class="equipment-qty">Qty: {{ $eq['quantity'] }}</span>
            </div>
            @endforeach
        </div>
        @endif
        
        <!-- Pricing -->
        <div class="section total-section">
            <div class="section-title">Pricing Details</div>
            <div class="total-row">
                <span class="total-label">Rate per Hour:</span>
                <span class="total-value"><span class="currency">₱</span>{{ number_format($reservation->price_per_hour ?? 0, 2) }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Duration:</span>
                <span class="total-value">
                    {{ \Carbon\Carbon::parse($reservation->start_date)->diffInHours($reservation->end_date) }} hours
                </span>
            </div>
            @if($reservation->equipment_details && count($reservation->equipment_details) > 0)
            <div class="total-row">
                <span class="total-label">Equipment Items:</span>
                <span class="total-value">{{ count($reservation->equipment_details) }} items</span>
            </div>
            @endif
            <div class="divider"></div>
            <div class="total-row">
                <span class="total-label">Final Total:</span>
                <span class="final-total"><span class="currency">₱</span>{{ number_format($reservation->final_price ?? 0, 2) }}</span>
            </div>
        </div>
        
        <!-- Status -->
        <div class="section">
            <div class="section-title">Reservation Status</div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    <span class="status-badge">Final Approved</span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Generated:</span>
                <span class="info-value">{{ now()->format('M d, Y g:i A') }}</span>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-text">Thank you for choosing GSU Reservation System</div>
            <div class="footer-text">This is an official receipt for your reservation</div>
            <div class="footer-text">Keep this receipt for your records</div>
        </div>
    </div>
</body>
</html> 