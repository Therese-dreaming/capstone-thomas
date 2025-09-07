@extends('layouts.drjavier')

@section('title', 'OTP Dashboard')
@section('page-title', 'OTP Dashboard')
@section('page-subtitle', 'Final Approval - Reservation Management')

<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

@section('content')
<div class="space-y-6 font-poppins">
    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="px-6 pt-6">
            <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                <button onclick="showOtpTab('overview')" id="otp-tab-overview" class="otp-tab-button active px-6 py-3 rounded-lg text-sm font-bold transition-all duration-300">
                    <i class="fas fa-chart-pie mr-2"></i>Overview
                </button>
                <button onclick="showOtpTab('finance')" id="otp-tab-finance" class="otp-tab-button px-6 py-3 rounded-lg text-sm font-bold transition-all duration-300">
                    <i class="fas fa-dollar-sign mr-2"></i>Finance
                </button>
                <button onclick="showOtpTab('trends')" id="otp-tab-trends" class="otp-tab-button px-6 py-3 rounded-lg text-sm font-bold transition-all duration-300">
                    <i class="fas fa-trending-up mr-2"></i>Trends
                </button>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Overview Tab -->
            <div id="otp-content-overview" class="otp-tab-content active">
                <!-- Hero Section -->
                <div class="bg-maroon rounded-xl p-6 text-white mb-8">
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-2xl font-bold font-poppins mb-2">Welcome, {{ Auth::user()->name ?? 'OTP' }}!</h1>
                            <p class="text-red-100 font-inter">Grant final approvals on Mhadel-approved reservations.</p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold font-poppins">{{ $stats['pending'] ?? 0 }}</div>
                            <div class="text-sm text-red-100 font-inter">Pending Final Review</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-inter">Pending Final Review</p>
                                <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $stats['pending'] ?? 0 }}</h3>
                                <p class="text-xs text-gray-500 font-inter">Awaiting OTP</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-inter">Approved Today</p>
                                <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $stats['approved'] ?? 0 }}</h3>
                                <p class="text-xs text-gray-500 font-inter">Final Approved</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-times-circle text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-inter">Rejected Today</p>
                                <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $stats['rejected'] ?? 0 }}</h3>
                                <p class="text-xs text-gray-500 font-inter">Final Rejected</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-inter">Total This Month</p>
                                <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $stats['total'] ?? 0 }}</h3>
                                <p class="text-xs text-gray-500 font-inter">All OTP Decisions</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <a href="{{ route('drjavier.reservations.index', ['status' => 'pending']) }}" class="bg-maroon text-white p-6 rounded-xl hover:bg-red-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <div class="flex items-center">
                            <i class="fas fa-clipboard-check text-3xl mr-4"></i>
                            <div>
                                <h3 class="text-lg font-bold font-poppins">Review Pending</h3>
                                <p class="text-red-100 font-inter">Mhadel-approved reservations</p>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('drjavier.reservations.index', ['status' => 'approved']) }}" class="bg-green-600 text-white p-6 rounded-xl hover:bg-green-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <div class="flex items-center">
                            <i class="fas fa-check text-3xl mr-4"></i>
                            <div>
                                <h3 class="text-lg font-bold font-poppins">Approved List</h3>
                                <p class="text-green-100 font-inter">Final approved reservations</p>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('drjavier.reservations.index', ['status' => 'rejected']) }}" class="bg-red-600 text-white p-6 rounded-xl hover:bg-red-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <div class="flex items-center">
                            <i class="fas fa-times text-3xl mr-4"></i>
                            <div>
                                <h3 class="text-lg font-bold font-poppins">Rejected List</h3>
                                <p class="text-red-100 font-inter">Final rejections</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Recent Reservations -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-history text-maroon mr-3"></i>
                            Recent Mhadel Approved
                        </h2>
                        <a href="{{ route('drjavier.reservations.index') }}" class="text-maroon hover:text-red-800 text-sm font-bold font-inter transition-colors">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-6">
                        @if(($recent_reservations ?? collect())->count() > 0)
                            <div class="space-y-4">
                                @foreach($recent_reservations as $reservation)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-300 border border-gray-100">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-3">
                                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold mr-3">Mhadel Approved</span>
                                                <span class="text-sm text-gray-500 font-inter">{{ $reservation->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="text-base font-bold text-gray-800 font-poppins mb-2">{{ $reservation->event_title }}</div>
                                            <div class="flex items-center text-sm text-gray-600 font-inter">
                                                <i class="fas fa-user mr-2 text-maroon"></i>
                                                <span>{{ $reservation->user->name }}</span>
                                                <span class="mx-3 text-gray-400">•</span>
                                                <i class="fas fa-calendar mr-2 text-maroon"></i>
                                                <span>{{ $reservation->start_date->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="{{ route('drjavier.reservations.show', $reservation->id) }}" class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-red-800 transition-all duration-300 font-medium" title="Review">
                                                <i class="fas fa-eye mr-2"></i>Review
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-calendar-check text-3xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-600 font-poppins mb-2">No Pending Approvals</h3>
                                <p class="text-gray-500 font-inter">All Mhadel approved reservations have been reviewed.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Finance Tab -->
            <div id="otp-content-finance" class="otp-tab-content hidden">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-600 font-inter font-medium">Total Revenue</p>
                                <h3 class="text-2xl font-bold text-green-800 font-poppins">₱{{ number_format($totalRevenue ?? 0) }}</h3>
                                <p class="text-xs text-green-600 font-inter">This month</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-blue-600 font-inter font-medium">Expected Revenue</p>
                                <h3 class="text-2xl font-bold text-blue-800 font-poppins">₱{{ number_format($expectedRevenue ?? 0) }}</h3>
                                <p class="text-xs text-blue-600 font-inter">Mhadel Approved</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clock text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-6 border border-yellow-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-yellow-700 font-inter font-medium">Avg Price / Reservation</p>
                                <h3 class="text-2xl font-bold text-yellow-900 font-poppins">₱{{ number_format($averageRevenue ?? 0) }}</h3>
                                <p class="text-xs text-yellow-700 font-inter">Final Approved</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-calculator text-yellow-700 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-fuchsia-50 rounded-xl p-6 border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-purple-700 font-inter font-medium">Revenue Growth</p>
                                <h3 class="text-2xl font-bold text-purple-900 font-poppins">{{ number_format($revenueGrowth ?? 0, 1) }}%</h3>
                                <p class="text-xs text-purple-700 font-inter">vs last month</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-chart-line text-purple-700 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800 font-poppins">Monthly Revenue Trend</h3>
                            <div class="flex space-x-2">
                                <button onclick="otpUpdateRevenueChart('monthly')" class="px-3 py-1 text-xs font-medium bg-maroon text-white rounded-lg">Monthly</button>
                                <button onclick="otpUpdateRevenueChart('quarterly')" class="px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Quarterly</button>
                            </div>
                        </div>
                        <div style="height: 280px;"><canvas id="otpChartRevenue"></canvas></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800 font-poppins">Expected Revenue Trend</h3>
                            <div class="flex space-x-2">
                                <button onclick="otpUpdateExpectedRevenueChart('monthly')" class="px-3 py-1 text-xs font-medium bg-maroon text-white rounded-lg">Monthly</button>
                                <button onclick="otpUpdateExpectedRevenueChart('quarterly')" class="px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Quarterly</button>
                            </div>
                        </div>
                        <div style="height: 280px;"><canvas id="otpChartExpectedRevenue"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- Trends Tab -->
            <div id="otp-content-trends" class="otp-tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $totalUsers ?? 0 }}</h3>
                        <p class="text-sm text-gray-600 font-inter">Total Users</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-building text-green-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $totalVenues ?? 0 }}</h3>
                        <p class="text-sm text-gray-600 font-inter">Active Venues</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 text-center">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-check text-yellow-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $totalReservations ?? 0 }}</h3>
                        <p class="text-sm text-gray-600 font-inter">Total Reservations</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clock text-purple-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $avgProcessingTime ?? 0 }}h</h3>
                        <p class="text-sm text-gray-600 font-inter">Avg Processing</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800 font-poppins">Department Distribution</h3>
                            <div class="flex space-x-2">
                                <button onclick="otpUpdateDepartmentChart('count')" class="px-3 py-1 text-xs font-medium bg-maroon text-white rounded-lg">By Count</button>
                                <button onclick="otpUpdateDepartmentChart('revenue')" class="px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg">By Revenue</button>
                            </div>
                        </div>
                        <div style="height: 280px;"><canvas id="otpChartDepartments"></canvas></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800 font-poppins">Venue Utilization</h3>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span class="text-xs text-gray-600">Hours Used</span>
                            </div>
                        </div>
                        <div style="height: 280px;"><canvas id="otpChartUtilization"></canvas></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.font-inter { font-family: 'Inter', sans-serif; }
.font-poppins { font-family: 'Poppins', sans-serif; }

/* Tab Styles */
.otp-tab-button { color: #6B7280; background: transparent; }
.otp-tab-button.active { color: #8B0000; background: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
.otp-tab-button:hover:not(.active) { color: #374151; background: rgba(139,0,0,0.05); }
.otp-tab-content { display: none; }
.otp-tab-content.active { display: block; animation: otpFadeIn 0.3s ease-in-out; }
@keyframes otpFadeIn { from { opacity:0; transform: translateY(10px);} to { opacity:1; transform: translateY(0);} }
</style>

<script>
// Minimal data (controller can override by injecting variables)
var otpLabelsMonths = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
var otpLabelsQuarters = ['Jan-Mar','Apr-Jun','Jul-Sep','Oct-Dec'];
var otpRevenueSeries = @json($revenueSeries ?? []);
var otpRevenueQuarterly = @json($revenueQuarterly ?? []);
var otpExpectedRevenueSeries = @json($expectedRevenueSeries ?? []);
var otpExpectedRevenueQuarterly = @json($expectedRevenueQuarterly ?? []);
var otpByDepartment = @json($byDepartment ?? []);
var otpUtilizationWeeks = @json($utilizationWeeks ?? []);

function otpPeso(v){ try { return '\u20B1' + Number(v||0).toLocaleString(); } catch(e){ return 'PHP ' + (v||0); } }
function otpPrepareCanvas(id, h){ var c=document.getElementById(id); if(!c) return null; c.style.height=h+'px'; c.style.maxHeight=h+'px'; if(Chart && Chart.getChart){ var old=Chart.getChart(c); if(old) old.destroy(); } return c; }
function otpOptions(){ return { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false }, tooltip:{ backgroundColor:'rgba(17,24,39,0.95)', titleColor:'#E5E7EB', bodyColor:'#E5E7EB', padding:12 } }, scales:{ x:{ grid:{ color:'rgba(17,24,39,0.06)', drawBorder:false }, ticks:{ color:'#6B7280', font:{ size:11 } } }, y:{ grid:{ color:'rgba(17,24,39,0.06)', drawBorder:false }, ticks:{ color:'#6B7280', font:{ size:11 } } } } }; }

function showOtpTab(name){
    document.querySelectorAll('.otp-tab-content').forEach(n=>{ n.classList.remove('active'); n.classList.add('hidden'); });
    document.querySelectorAll('.otp-tab-button').forEach(n=>n.classList.remove('active'));
    var content=document.getElementById('otp-content-'+name); if(content){ content.classList.remove('hidden'); content.classList.add('active'); }
    var btn=document.getElementById('otp-tab-'+name); if(btn){ btn.classList.add('active'); }
    if(name==='finance' || name==='trends'){ otpInitCharts(); }
}

function otpInitCharts(){ if(window.otpChartsInitialized) return; 
    var rev=otpPrepareCanvas('otpChartRevenue',280); if(rev){ new Chart(rev,{ type:'line', data:{ labels:otpLabelsMonths, datasets:[{ label:'Revenue', data:otpRevenueSeries, borderColor:'#8B1818', backgroundColor:'rgba(139,24,24,0.1)', fill:true, tension:0.4, pointRadius:4, pointHoverRadius:6, borderWidth:3 }] }, options:(function(){ var o=otpOptions(); o.scales.y.ticks.callback=function(v){return otpPeso(v)}; return o; })() }); }
    var exp=otpPrepareCanvas('otpChartExpectedRevenue',280); if(exp){ new Chart(exp,{ type:'line', data:{ labels:otpLabelsMonths, datasets:[{ label:'Expected', data:otpExpectedRevenueSeries, borderColor:'#2563EB', backgroundColor:'rgba(37,99,235,0.1)', fill:true, tension:0.4, pointRadius:4, pointHoverRadius:6, borderWidth:3 }] }, options:(function(){ var o=otpOptions(); o.scales.y.ticks.callback=function(v){return otpPeso(v)}; return o; })() }); }
    var dept=otpPrepareCanvas('otpChartDepartments',280); if(dept){ if(!otpByDepartment || otpByDepartment.length===0){ dept.style.display='none'; var empty=document.createElement('div'); empty.className='flex items-center justify-center h-full'; empty.innerHTML='<div class="text-center py-8"><i class="fas fa-building text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-600">No department data available</p></div>'; dept.parentNode.appendChild(empty); } else { new Chart(dept,{ type:'bar', data:{ labels:otpByDepartment.map(d=>d.department), datasets:[{ label:'Reservations', data:otpByDepartment.map(d=>d.count), backgroundColor:['#2563EB','#10B981','#8B1818','#F59E0B','#6B7280','#A855F7'], borderColor:'#ffffff', borderWidth:1 }] }, options:otpOptions() }); } }
    var util=otpPrepareCanvas('otpChartUtilization',280); if(util){ if(!otpUtilizationWeeks || otpUtilizationWeeks.length===0){ util.style.display='none'; var empty2=document.createElement('div'); empty2.className='flex items-center justify-center h-full'; empty2.innerHTML='<div class="text-center py-8"><i class="fas fa-chart-line text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-600">No utilization data available</p></div>'; util.parentNode.appendChild(empty2); } else { new Chart(util,{ type:'line', data:{ labels:otpUtilizationWeeks.map(function(u){ return (u.label || ('W'+u.week)); }), datasets:[{ data:otpUtilizationWeeks.map(function(u){ return u.hours; }), borderColor:'#2563EB', backgroundColor:'rgba(37,99,235,0.1)', fill:true, tension:0.4, pointRadius:4, pointHoverRadius:6, borderWidth:3 }] }, options:otpOptions() }); } }
    window.otpChartsInitialized=true;
}

function otpUpdateRevenueChart(period){ var c=Chart.getChart('otpChartRevenue'); if(!c) return; if(period==='monthly'){ c.data.labels=otpLabelsMonths; c.data.datasets[0].data=otpRevenueSeries; } else { c.data.labels=otpLabelsQuarters; c.data.datasets[0].data=otpRevenueQuarterly; } c.update(); }
function otpUpdateExpectedRevenueChart(period){ var c=Chart.getChart('otpChartExpectedRevenue'); if(!c) return; if(period==='monthly'){ c.data.labels=otpLabelsMonths; c.data.datasets[0].data=otpExpectedRevenueSeries; } else { c.data.labels=otpLabelsQuarters; c.data.datasets[0].data=otpExpectedRevenueQuarterly; } c.update(); }
function otpUpdateDepartmentChart(metric){ var c=Chart.getChart('otpChartDepartments'); if(!c) return; if(metric==='count'){ c.data.labels=otpByDepartment.map(d=>d.department); c.data.datasets[0].data=otpByDepartment.map(d=>d.count); c.options.plugins.tooltip={}; } c.update(); }

document.addEventListener('DOMContentLoaded', function(){ showOtpTab('overview'); });
</script>
@endsection