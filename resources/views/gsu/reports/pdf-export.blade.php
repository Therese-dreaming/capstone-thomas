<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GSU Reports Export</title>
    <style>
        @page {
            margin: 25mm 20mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #333;
            line-height: 1.4;
            max-width: 75%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #800000;
            position: relative;
        }
        .header-logo {
            height: 40px;
            width: auto;
            vertical-align: middle;
            margin-right: 10px;
        }
        .header h1 {
            color: #800000;
            font-size: 16px;
            margin-bottom: 3px;
            display: inline-block;
            vertical-align: middle;
        }
        .header p {
            color: #666;
            font-size: 8px;
        }
        .summary-box {
            background: #f8f8f8;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .summary-box strong {
            color: #800000;
            font-size: 11px;
        }
        .charts-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .chart-item {
            display: table-cell;
            width: 50%;
            padding: 5px;
            vertical-align: top;
        }
        .chart-box {
            border: 2px solid #ddd;
            padding: 10px;
            background: #fff;
            height: 180px;
        }
        .chart-title {
            font-size: 10px;
            font-weight: bold;
            color: #800000;
            margin-bottom: 8px;
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .chart-content {
            font-size: 7px;
        }
        .bar-chart {
            margin-top: 5px;
        }
        .bar-item {
            margin-bottom: 3px;
        }
        .bar-label {
            display: inline-block;
            width: 35%;
            font-size: 7px;
        }
        .bar-visual {
            display: inline-block;
            height: 10px;
            background: #800000;
            vertical-align: middle;
        }
        .bar-value {
            display: inline-block;
            margin-left: 3px;
            font-size: 7px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 7px;
        }
        th {
            background: #800000;
            color: white;
            padding: 4px;
            text-align: left;
            font-size: 7px;
        }
        td {
            padding: 3px 4px;
            border-bottom: 1px solid #eee;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            text-align: center;
            font-size: 7px;
            color: #999;
            margin-top: 15px;
        }
        .charts-header {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #800000;
            margin-top: 25px;
            margin-bottom: 15px;
            padding: 8px;
            border-top: 2px solid #800000;
            border-bottom: 2px solid #800000;
        }
        .charts-logo {
            height: 28px;
            width: auto;
            vertical-align: middle;
            margin-right: 10px;
            position: relative;
            top: 2px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <img src="{{ public_path('images/pcclogo.png') }}" alt="PCC Logo" class="header-logo">
        <h1>GSU REPORTS & ANALYTICS</h1>
        <p>
            @if($exportType === 'both')
                Reservations & Events Combined Report
            @elseif($exportType === 'reservations')
                Reservations Report
            @else
                Events Report
            @endif
            @if($dateRange['start'] && $dateRange['end'])
                | {{ \Carbon\Carbon::parse($dateRange['start'])->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateRange['end'])->format('M d, Y') }}
            @endif
            | Generated: {{ now()->format('F d, Y h:i A') }}
        </p>
    </div>

    @if($includeSummary && $chartOption !== 'only')
        <!-- Summary Section -->
        <div class="summary-box">
            @if($exportType === 'reservations' || $exportType === 'both')
                <strong>Reservations: {{ $reservations->count() }}</strong> | 
                Revenue: ₱{{ number_format($reservations->where('status', 'completed')->sum('final_price'), 2) }}
            @endif
            @if($exportType === 'both')
                 | 
            @endif
            @if($exportType === 'events' || $exportType === 'both')
                <strong>Events: {{ $events->count() }}</strong>
            @endif
        </div>
    @endif

    @if($chartOption !== 'only')
        <!-- Data Tables -->
        @if($exportType === 'both')
            <!-- Combined Table for Both Reservations and Events -->
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Person</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $reservation)
                        <tr>
                            <td>Reservation</td>
                            <td>{{ $reservation->reservation_id ?? $reservation->id }}</td>
                            <td>{{ \Str::limit($reservation->event_title ?? 'N/A', 25) }}</td>
                            <td>{{ $reservation->start_date ? $reservation->start_date->format('M d') : 'N/A' }}</td>
                            <td>{{ \Str::limit($reservation->venue->name ?? 'N/A', 15) }}</td>
                            <td>{{ \Str::limit($reservation->user->name ?? 'N/A', 15) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</td>
                        </tr>
                    @empty
                    @endforelse
                    @forelse($events as $event)
                        @php
                            $now = now();
                            $status = 'Unknown';
                            if ($event->start_date && $event->end_date) {
                                if ($now->isBefore($event->start_date)) {
                                    $status = 'Upcoming';
                                } elseif ($now->isBetween($event->start_date, $event->end_date)) {
                                    $status = 'Ongoing';
                                } else {
                                    $status = 'Completed';
                                }
                            }
                        @endphp
                        <tr style="background-color: #f5f5f5;">
                            <td>Event</td>
                            <td>{{ $event->event_id ?? $event->id }}</td>
                            <td>{{ \Str::limit($event->title ?? 'N/A', 25) }}</td>
                            <td>{{ $event->start_date ? $event->start_date->format('M d') : 'N/A' }}</td>
                            <td>{{ \Str::limit($event->venue->name ?? 'N/A', 15) }}</td>
                            <td>{{ \Str::limit($event->organizer ?? 'N/A', 15) }}</td>
                            <td>{{ $status }}</td>
                        </tr>
                    @empty
                    @endforelse
                    @if($reservations->isEmpty() && $events->isEmpty())
                        <tr>
                            <td colspan="7" style="text-align: center; color: #999;">No data</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @elseif($exportType === 'reservations')
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event Title</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Requester</th>
                        <th>Status</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $reservation)
                        <tr>
                            <td>{{ $reservation->reservation_id ?? $reservation->id }}</td>
                            <td>{{ \Str::limit($reservation->event_title ?? 'N/A', 25) }}</td>
                            <td>{{ $reservation->start_date ? $reservation->start_date->format('M d') : 'N/A' }}</td>
                            <td>{{ \Str::limit($reservation->venue->name ?? 'N/A', 15) }}</td>
                            <td>{{ \Str::limit($reservation->user->name ?? 'N/A', 15) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</td>
                            <td>₱{{ number_format($reservation->final_price ?? 0, 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #999;">No data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @elseif($exportType === 'events')
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Organizer</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        @php
                            $now = now();
                            $status = 'Unknown';
                            if ($event->start_date && $event->end_date) {
                                if ($now->isBefore($event->start_date)) {
                                    $status = 'Upcoming';
                                } elseif ($now->isBetween($event->start_date, $event->end_date)) {
                                    $status = 'Ongoing';
                                } else {
                                    $status = 'Completed';
                                }
                            }
                        @endphp
                        <tr>
                            <td>{{ $event->event_id ?? $event->id }}</td>
                            <td>{{ \Str::limit($event->title ?? 'N/A', 30) }}</td>
                            <td>{{ $event->start_date ? $event->start_date->format('M d') : 'N/A' }}</td>
                            <td>{{ \Str::limit($event->venue->name ?? 'N/A', 15) }}</td>
                            <td>{{ \Str::limit($event->organizer ?? 'N/A', 15) }}</td>
                            <td>{{ $status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #999;">No data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    @endif

    @if($chartOption === 'include' || $chartOption === 'only')
        <!-- Charts Section -->
        @if($chartOption === 'only' || ($exportType === 'both' && $chartOption === 'include'))
            <div class="page-break"></div>
        @endif

        <div class="charts-header">
            <img src="{{ public_path('images/pcclogo.png') }}" alt="PCC Logo" class="charts-logo">
            ANALYTICS CHARTS
        </div>

        @if($exportType === 'both')
            @php
                // Reservations data
                $statusCounts = $reservations->groupBy('status')->map->count();
                $monthlyData = $reservations->groupBy(function($item) {
                    return $item->start_date ? $item->start_date->format('M Y') : 'Unknown';
                })->map->count()->take(4);
                $maxStatus = $statusCounts->max() ?: 1;
                $maxMonthly = $monthlyData->max() ?: 1;
                
                // Events data
                $now = now();
                $eventStatusCounts = $events->groupBy(function($event) use ($now) {
                    if (!$event->start_date || !$event->end_date) return 'Unknown';
                    if ($now->isBefore($event->start_date)) return 'Upcoming';
                    if ($now->isBetween($event->start_date, $event->end_date)) return 'Ongoing';
                    return 'Completed';
                })->map->count();
                $eventMonthlyData = $events->groupBy(function($item) {
                    return $item->start_date ? $item->start_date->format('M Y') : 'Unknown';
                })->map->count()->take(4);
                $maxEventStatus = $eventStatusCounts->max() ?: 1;
                $maxEventMonthly = $eventMonthlyData->max() ?: 1;
            @endphp
            
            <!-- Combined Charts on One Page -->
            <div class="charts-grid">
                <!-- Reservations Status Bar Chart -->
                <div class="chart-item">
                    <div class="chart-box">
                        <div class="chart-title">Reservations by Status</div>
                        <div class="bar-chart">
                            @foreach($statusCounts as $status => $count)
                                <div class="bar-item">
                                    <span class="bar-label">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                    <span class="bar-visual" style="width: {{ ($count / $maxStatus) * 80 }}px;"></span>
                                    <span class="bar-value">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Events Status Bar Chart -->
                <div class="chart-item">
                    <div class="chart-box">
                        <div class="chart-title">Events by Status</div>
                        <div class="bar-chart">
                            @foreach($eventStatusCounts as $status => $count)
                                <div class="bar-item">
                                    <span class="bar-label">{{ $status }}</span>
                                    <span class="bar-visual" style="width: {{ ($count / $maxEventStatus) * 80 }}px;"></span>
                                    <span class="bar-value">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Trends -->
            <div class="charts-grid">
                <!-- Reservations Monthly Trend -->
                <div class="chart-item">
                    <div class="chart-box">
                        <div class="chart-title">Reservations Monthly Trend</div>
                        <div class="bar-chart">
                            @foreach($monthlyData as $month => $count)
                                <div class="bar-item">
                                    <span class="bar-label">{{ $month }}</span>
                                    <span class="bar-visual" style="width: {{ ($count / $maxMonthly) * 80 }}px;"></span>
                                    <span class="bar-value">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Events Monthly Trend -->
                <div class="chart-item">
                    <div class="chart-box">
                        <div class="chart-title">Events Monthly Trend</div>
                        <div class="bar-chart">
                            @foreach($eventMonthlyData as $month => $count)
                                <div class="bar-item">
                                    <span class="bar-label">{{ $month }}</span>
                                    <span class="bar-visual" style="width: {{ ($count / $maxEventMonthly) * 80 }}px;"></span>
                                    <span class="bar-value">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        @elseif($exportType === 'reservations')
            @php
                $statusCounts = $reservations->groupBy('status')->map->count();
                $monthlyData = $reservations->groupBy(function($item) {
                    return $item->start_date ? $item->start_date->format('M Y') : 'Unknown';
                })->map->count()->take(6);
                $revenueData = $reservations->where('status', 'completed')->groupBy(function($item) {
                    return $item->start_date ? $item->start_date->format('M Y') : 'Unknown';
                })->map(function($items) {
                    return $items->sum('final_price');
                })->take(6);
                $maxStatus = $statusCounts->max() ?: 1;
                $maxMonthly = $monthlyData->max() ?: 1;
                $maxRevenue = $revenueData->max() ?: 1;
            @endphp
            
            <!-- Reservations Charts Grid -->
            <div class="charts-grid">
                <!-- Chart 1: Status Bar Chart -->
                <div class="chart-item">
                    <div class="chart-box">
                        <div class="chart-title">Reservations by Status</div>
                        <div class="bar-chart">
                            @foreach($statusCounts as $status => $count)
                                <div class="bar-item">
                                    <span class="bar-label">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                    <span class="bar-visual" style="width: {{ ($count / $maxStatus) * 100 }}px;"></span>
                                    <span class="bar-value">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Chart 2: Monthly Trend Bar Chart -->
                <div class="chart-item">
                    <div class="chart-box">
                        <div class="chart-title">Monthly Trend (Last 6 Months)</div>
                        <div class="bar-chart">
                            @foreach($monthlyData as $month => $count)
                                <div class="bar-item">
                                    <span class="bar-label">{{ $month }}</span>
                                    <span class="bar-visual" style="width: {{ ($count / $maxMonthly) * 100 }}px;"></span>
                                    <span class="bar-value">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart (Full Width) -->
            <div class="chart-item" style="display: block; width: 100%; padding: 5px;">
                <div class="chart-box" style="height: 140px;">
                    <div class="chart-title">Revenue Trend (Last 6 Months)</div>
                    <div class="bar-chart">
                        @foreach($revenueData as $month => $revenue)
                            <div class="bar-item">
                                <span class="bar-label">{{ $month }}</span>
                                <span class="bar-visual" style="width: {{ ($revenue / $maxRevenue) * 150 }}px;"></span>
                                <span class="bar-value">₱{{ number_format($revenue, 0) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        @elseif($exportType === 'events')
            @php
                $now = now();
                $eventStatusCounts = $events->groupBy(function($event) use ($now) {
                    if (!$event->start_date || !$event->end_date) return 'Unknown';
                    if ($now->isBefore($event->start_date)) return 'Upcoming';
                    if ($now->isBetween($event->start_date, $event->end_date)) return 'Ongoing';
                    return 'Completed';
                })->map->count();
                $eventMonthlyData = $events->groupBy(function($item) {
                    return $item->start_date ? $item->start_date->format('M Y') : 'Unknown';
                })->map->count()->take(6);
                $deptData = $events->groupBy('department')->map->count()->take(6);
                $maxEventStatus = $eventStatusCounts->max() ?: 1;
                $maxEventMonthly = $eventMonthlyData->max() ?: 1;
                $maxDept = $deptData->max() ?: 1;
            @endphp
            
            <!-- Events Charts Grid -->
            <div class="charts-grid">
                <!-- Chart 1: Status Bar Chart -->
                <div class="chart-item">
                    <div class="chart-box">
                        <div class="chart-title">Events by Status</div>
                        <div class="bar-chart">
                            @foreach($eventStatusCounts as $status => $count)
                                <div class="bar-item">
                                    <span class="bar-label">{{ $status }}</span>
                                    <span class="bar-visual" style="width: {{ ($count / $maxEventStatus) * 100 }}px;"></span>
                                    <span class="bar-value">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Chart 2: Monthly Trend Bar Chart -->
                <div class="chart-item">
                    <div class="chart-box">
                        <div class="chart-title">Monthly Trend (Last 6 Months)</div>
                        <div class="bar-chart">
                            @foreach($eventMonthlyData as $month => $count)
                                <div class="bar-item">
                                    <span class="bar-label">{{ $month }}</span>
                                    <span class="bar-visual" style="width: {{ ($count / $maxEventMonthly) * 100 }}px;"></span>
                                    <span class="bar-value">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Department Chart (Full Width) -->
            <div class="chart-item" style="display: block; width: 100%; padding: 5px;">
                <div class="chart-box" style="height: 140px;">
                    <div class="chart-title">Events by Department (Top 6)</div>
                    <div class="bar-chart">
                        @foreach($deptData as $dept => $count)
                            <div class="bar-item">
                                <span class="bar-label">{{ $dept ?? 'N/A' }}</span>
                                <span class="bar-visual" style="width: {{ ($count / $maxDept) * 150 }}px;"></span>
                                <span class="bar-value">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>GSU - General Services Unit | Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>
