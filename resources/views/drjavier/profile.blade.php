@extends('layouts.drjavier')

@section('title', 'Profile - PPGS')
@section('page-title', 'Profile')
@section('page-subtitle', 'Manage your account information')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root { --maroon: #8B1818; --maroon-dark: #6f1313; --bg: #F4F5F7; }
    body { font-family: 'Poppins', sans-serif; }
    .font-poppins { font-family: 'Poppins', sans-serif; }
    .font-montserrat { font-family: 'Montserrat', sans-serif; }
    .bg-maroon { background-color: var(--maroon); }
    .text-maroon { color: var(--maroon); }
    .border-maroon { border-color: var(--maroon); }
    .tab-content { 
        display: none;
    }
    .tab-content.active { 
        display: block !important; 
        animation: fadeIn 0.3s ease-in-out; 
    }
    @keyframes fadeIn { 
        from { opacity: 0; transform: translateY(10px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
</style>
@endsection

@section('content')
<div class="min-h-screen font-poppins profile-page" style="background-color: var(--bg);">
    <div class="max-w-4xl mx-auto px-4 py-6">
        <!-- Profile Header -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <!-- Header with solid color -->
            <div class="bg-maroon p-6 text-white">
                <div class="flex flex-col lg:flex-row items-center lg:items-start space-y-4 lg:space-y-0 lg:space-x-6">
                    <!-- Avatar -->
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center border-2 border-white/30">
                        <i class="fas fa-user-tie text-white text-xl"></i>
                    </div>
                    
                    <!-- Profile Info -->
                    <div class="flex-1 text-center lg:text-left">
                        <h1 class="text-2xl font-bold mb-1">{{ $user->name ?? 'PPGS Representative' }}</h1>
                        <p class="text-lg text-white/90 mb-3">{{ $user->email }}</p>
                        
                        <div class="flex flex-wrap justify-center lg:justify-start gap-2 mb-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-100 border border-green-400/30">
                                <i class="fas fa-check-circle mr-1"></i>
                                Account Active
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white border border-white/30">
                                <i class="fas fa-building mr-1"></i>
                                Physical Plan & General Service
                            </span>
                        </div>
                        
                        <p class="text-xs text-white/80">
                            <i class="fas fa-clock mr-1"></i>
                            Last updated: {{ optional($user->updated_at)->diffForHumans() ?? '—' }}
                        </p>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 w-full lg:w-auto">
                        <div class="text-center p-3 bg-white/10 rounded-lg border border-white/20">
                            <div class="text-lg font-bold">{{ $user->reservations_count ?? 0 }}</div>
                            <div class="text-xs opacity-90">Reviewed</div>
                        </div>
                        <div class="text-center p-3 bg-white/10 rounded-lg border border-white/20">
                            <div class="text-lg font-bold">{{ $user->approved_count ?? 0 }}</div>
                            <div class="text-xs opacity-90">Approved</div>
                        </div>
                        <div class="text-center p-3 bg-white/10 rounded-lg border border-white/20">
                            <div class="text-lg font-bold">{{ $user->rejected_count ?? 0 }}</div>
                            <div class="text-xs opacity-90">Rejected</div>
                        </div>
                        <div class="text-center p-3 bg-white/10 rounded-lg border border-white/20">
                            <div class="text-lg font-bold">{{ $user->pending_count ?? 0 }}</div>
                            <div class="text-xs opacity-90">Pending</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Tabs -->
            <div class="px-4 py-3 bg-gray-50">
                <div class="flex flex-wrap gap-2">
                    <button onclick="showTab('account')" id="tab-account" 
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 bg-maroon text-white">
                        <i class="fas fa-user mr-1"></i>Account
                    </button>
                    <button onclick="showTab('security')" id="tab-security" 
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 text-gray-600 hover:bg-gray-200">
                        <i class="fas fa-shield-alt mr-1"></i>Security
                    </button>
                    <button onclick="showTab('activity')" id="tab-activity" 
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 text-gray-600 hover:bg-gray-200">
                        <i class="fas fa-chart-line mr-1"></i>Activity
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <span class="text-green-700 font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <span class="text-red-700 font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Account Tab -->
            <div id="content-account" class="tab-content active">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-gray-800 mb-1 flex items-center">
                        <i class="fas fa-user-edit mr-2 text-maroon"></i>
                        Account Information
                    </h2>
                    <p class="text-sm text-gray-600">Update your personal information and preferences</p>
                </div>

                <form method="POST" action="{{ route('drjavier.profile.update') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors @error('name') border-red-500 @enderror" required />
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors @error('email') border-red-500 @enderror" required />
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors @error('phone') border-red-500 @enderror" />
                        @error('phone')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-8 py-3 bg-maroon text-white rounded-xl hover:bg-red-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Tab -->
            <div id="content-security" class="tab-content">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-gray-800 mb-1 flex items-center">
                        <i class="fas fa-shield-alt mr-2 text-maroon"></i>
                        Security Settings
                    </h2>
                    <p class="text-sm text-gray-600">Manage your password and account security</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-maroon text-lg mr-2 mt-1"></i>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-1">Password Requirements</h3>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Minimum 8 characters in length</li>
                                <li>• At least one uppercase letter</li>
                                <li>• At least one lowercase letter</li>
                                <li>• At least one number</li>
                                <li>• At least one special character</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('drjavier.profile.password') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" name="current_password" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors @error('current_password') border-red-500 @enderror" required />
                        <p class="text-xs text-gray-500 mt-2">Enter your current password to confirm changes</p>
                        @error('current_password')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" name="password" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors @error('password') border-red-500 @enderror" required />
                            @error('password')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input type="password" name="password_confirmation" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors" required />
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-8 py-3 bg-maroon text-white rounded-xl hover:bg-red-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-key mr-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Activity Tab -->
            <div id="content-activity" class="tab-content">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-gray-800 mb-1 flex items-center">
                        <i class="fas fa-chart-line mr-2 text-maroon"></i>
                        Activity Overview
                    </h2>
                    <p class="text-sm text-gray-600">View your approval statistics and recent activity</p>
                </div>

                <!-- Metrics Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-4 bg-blue-600 rounded-lg text-white">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-calendar-alt text-white text-xl"></i>
                        </div>
                        <div class="text-2xl font-bold text-white mb-1">{{ $user->reservations_count ?? 0 }}</div>
                        <div class="text-sm text-blue-100">Total Reviewed</div>
                    </div>
                    
                    <div class="text-center p-4 bg-green-600 rounded-lg text-white">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-check-circle text-white text-xl"></i>
                        </div>
                        <div class="text-2xl font-bold text-white mb-1">{{ $user->approved_count ?? 0 }}</div>
                        <div class="text-sm text-green-100">Approved</div>
                    </div>
                    
                    <div class="text-center p-4 bg-red-600 rounded-lg text-white">
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-times-circle text-white text-xl"></i>
                        </div>
                        <div class="text-2xl font-bold text-white mb-1">{{ $user->rejected_count ?? 0 }}</div>
                        <div class="text-sm text-red-100">Rejected</div>
                    </div>
                    
                    <div class="text-center p-4 bg-yellow-600 rounded-lg text-white">
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <div class="text-2xl font-bold text-white mb-1">{{ $user->pending_count ?? 0 }}</div>
                        <div class="text-sm text-yellow-100">Pending</div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Recent Activity</h3>
                    @php
                        $recentReservations = \App\Models\Reservation::whereIn('status', ['approved_OTP', 'rejected_OTP'])
                            ->where('updated_at', '>=', now()->subDays(30))
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp

                    @if($recentReservations->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentReservations as $reservation)
                                <div class="bg-gray-50 rounded-xl p-6 border-l-4 border-maroon hover:bg-gray-100 transition-colors duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-800 mb-1">{{ $reservation->event_title }}</h4>
                                            <p class="text-gray-600 text-sm mb-2">{{ $reservation->venue->name ?? 'No venue' }}</p>
                                            <p class="text-gray-500 text-xs">
                                                <i class="fas fa-calendar mr-1"></i>
                                                {{ $reservation->start_date ? $reservation->start_date->format('M d, Y') : 'No date' }}
                                            </p>
                                        </div>
                                        <div>
                                            @switch($reservation->status)
                                                @case('approved_OTP')
                                                    <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                        <i class="fas fa-check-circle mr-1"></i> Approved
                                                    </span>
                                                    @break
                                                @case('rejected_OTP')
                                                    <span class="px-3 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                        <i class="fas fa-times-circle mr-1"></i> Rejected
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                        {{ ucfirst(str_replace('_OTP', '', $reservation->status)) }}
                                                    </span>
                                            @endswitch
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="text-center mt-6">
                            <a href="{{ route('drjavier.reservations.index') }}" 
                               class="inline-flex items-center px-6 py-3 bg-maroon text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                View All Reservations
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-xl">
                            <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
                            </div>
                            <p class="text-gray-600 text-lg mb-4">No recent activity</p>
                            <a href="{{ route('drjavier.reservations.index') }}" 
                               class="inline-flex items-center px-6 py-3 bg-maroon text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                View All Reservations
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-bolt mr-2 text-maroon"></i>
                Quick Actions
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('drjavier.dashboard') }}" class="group bg-gray-50 rounded-xl p-6 border border-gray-200 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-maroon rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-tachometer-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-1">Dashboard</h3>
                            <p class="text-sm text-gray-600">Return to main dashboard</p>
                        </div>
                    </div>
                </a>
                
                <a href="{{ route('drjavier.reservations.index') }}" class="group bg-gray-50 rounded-xl p-6 border border-gray-200 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-maroon rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-calendar-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-1">Reservations</h3>
                            <p class="text-sm text-gray-600">Manage reservation requests</p>
                        </div>
                    </div>
                </a>
                
                <a href="{{ route('drjavier.gsu-reports') }}" class="group bg-gray-50 rounded-xl p-6 border border-gray-200 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-maroon rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-1">GSU Reports</h3>
                            <p class="text-sm text-gray-600">View and manage reports</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showTab(name) {
        console.log('Switching to tab:', name);
        
        // Hide all tab panels
        document.querySelectorAll('.tab-content').forEach(function(el) {
            el.classList.remove('active');
            el.style.display = 'none';
            console.log('Hiding tab:', el.id);
        });
        
        // Deactivate all nav buttons
        document.querySelectorAll('button[id^="tab-"]').forEach(function(btn) {
            btn.classList.remove('bg-maroon', 'text-white', 'shadow-md');
            btn.classList.add('text-gray-600', 'hover:bg-gray-200');
        });
        
        // Show selected tab panel and activate button
        const content = document.getElementById('content-' + name);
        const tab = document.getElementById('tab-' + name);
        
        console.log('Content element:', content);
        console.log('Tab element:', tab);
        
        if (content) {
            content.classList.add('active');
            content.style.display = 'block';
            console.log('Showing content:', content.id);
        }
        
        if (tab) {
            tab.classList.add('bg-maroon', 'text-white', 'shadow-md');
            tab.classList.remove('text-gray-600', 'hover:bg-gray-200');
            console.log('Activating tab:', tab.id);
        }
    }
    
    // Initialize tabs on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing tabs');
        
        // Hide all tabs first
        document.querySelectorAll('.tab-content').forEach(function(el) {
            el.classList.remove('active');
            el.style.display = 'none';
        });
        
        // Show only the account tab
        const accountTab = document.getElementById('content-account');
        if (accountTab) {
            accountTab.classList.add('active');
            accountTab.style.display = 'block';
        }
        
        // Set up tab button states
        document.querySelectorAll('button[id^="tab-"]').forEach(function(btn) {
            btn.classList.remove('bg-maroon', 'text-white', 'shadow-md');
            btn.classList.add('text-gray-600', 'hover:bg-gray-200');
        });
        
        // Activate the account tab button
        const accountButton = document.getElementById('tab-account');
        if (accountButton) {
            accountButton.classList.add('bg-maroon', 'text-white', 'shadow-md');
            accountButton.classList.remove('text-gray-600', 'hover:bg-gray-200');
        }
        
        console.log('Tabs initialized successfully');
    });
</script>
@endsection
