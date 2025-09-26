@extends('layouts.user')

@section('title', 'Profile')
@section('page-title', 'Profile')
@section('page-subtitle', 'Manage your account information')

@section('styles')
<style>
    .profile-header {
        background-image: linear-gradient(to right, var(--tw-gradient-from), var(--tw-gradient-to));
        --tw-gradient-from: #800000;
        --tw-gradient-to: #991b1b;
    }
    .tab-button {
        transition: all 0.2s ease;
    }
    .tab-button.active {
        background-color: white;
        color: #800000;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }
    .action-card {
        transition: all 0.3s ease;
    }
    .action-card:hover {
        transform: translateY(-3px);
    }
    /* Scoped input styling for this page */
    .profile-page input[type="text"],
    .profile-page input[type="email"],
    .profile-page input[type="password"],
    .profile-page input[type="tel"],
    .profile-page input[type="number"],
    .profile-page select,
    .profile-page textarea {
        border-width: 2px;
        border-color:rgb(237, 239, 241); /* gray-300 */
        height: 44px; /* ~ h-11 */
        padding-left: 0.75rem; /* pl-3 */
        padding-right: 0.75rem; /* pr-3 */
    }
    .profile-page textarea { height: auto; min-height: 120px; padding-top: 0.5rem; padding-bottom: 0.5rem; }
    .profile-page input:focus,
    .profile-page select:focus,
    .profile-page textarea:focus {
        outline: none;
        border-color: #8B1818; /* maroon */
        box-shadow: 0 0 0 3px rgba(139,24,24,0.15);
    }
</style>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6 font-poppins profile-page">
    <!-- Profile Header -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="profile-header p-6 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-semibold font-montserrat">{{ $user->name }}</h3>
                        <p class="text-white/90">{{ $user->email }}</p>
                        <div class="flex items-center mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $user->email_verified_at ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-yellow-100 text-yellow-700 border border-yellow-300' }}">
                                <i class="fas {{ $user->email_verified_at ? 'fa-check-circle' : 'fa-exclamation-circle' }} mr-1.5"></i>
                                {{ $user->email_verified_at ? 'Email Verified' : 'Not Verified' }}
                            </span>
                            <span class="ml-2 text-xs text-white/80">Member since {{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 md:mt-0 text-right">
                    <div class="text-sm text-white/80">Last updated</div>
                    <div class="text-base font-semibold">{{ $user->updated_at?->diffForHumans() ?? '—' }}</div>
                </div>
            </div>
        </div>
        
        <div class="px-4 py-3 bg-white">
            <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                <button type="button" onclick="showTab('account')" id="tab-account" class="tab-button active px-5 py-2 rounded-md text-sm font-semibold transition">
                    <i class="fas fa-user mr-2"></i>Account
                </button>
                <button type="button" onclick="showTab('security')" id="tab-security" class="tab-button px-5 py-2 rounded-md text-sm font-semibold transition text-gray-600">
                    <i class="fas fa-lock mr-2"></i>Security
                </button>
                <button type="button" onclick="showTab('stats')" id="tab-stats" class="tab-button px-5 py-2 rounded-md text-sm font-semibold transition text-gray-600">
                    <i class="fas fa-chart-bar mr-2"></i>Statistics
                </button>
            </div>
        </div>
    </div>

    <!-- Tab Contents -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Account Tab -->
        <div id="content-account" class="tab-content active space-y-6">
            <div class="border-b border-gray-200 pb-4 mb-4">
                <h2 class="text-xl font-bold text-gray-800 font-montserrat">Account Information</h2>
                <p class="text-gray-600 text-sm">Update your personal information</p>
            </div>

            <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name ?? '') }}" 
                            class="w-full border-gray-300 rounded-lg focus:ring-maroon focus:border-maroon" />
                        @error('first_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name ?? '') }}" 
                            class="w-full border-gray-300 rounded-lg focus:ring-maroon focus:border-maroon" />
                        @error('last_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                        class="w-full border-gray-300 rounded-lg focus:ring-maroon focus:border-maroon" />
                    @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                        class="w-full border-gray-300 rounded-lg focus:ring-maroon focus:border-maroon" />
                    @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>


                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-maroon text-white rounded-lg hover:bg-red-800 transition-colors shadow-sm">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Security Tab -->
        <div id="content-security" class="tab-content space-y-6">
            <div class="border-b border-gray-200 pb-4 mb-4">
                <h2 class="text-xl font-bold text-gray-800 font-montserrat">Security Settings</h2>
                <p class="text-gray-600 text-sm">Manage your password and account security</p>
            </div>

            <form method="POST" action="{{ route('user.password.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" 
                        class="w-full border-gray-300 rounded-lg focus:ring-maroon focus:border-maroon" />
                    @error('current_password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" name="password" 
                            class="w-full border-gray-300 rounded-lg focus:ring-maroon focus:border-maroon" />
                        @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" 
                            class="w-full border-gray-300 rounded-lg focus:ring-maroon focus:border-maroon" />
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-maroon text-white rounded-lg hover:bg-red-800 transition-colors shadow-sm">
                        <i class="fas fa-key mr-2"></i>Update Password
                    </button>
                </div>
            </form>

            @if(!$user->email_verified_at)
                <div class="mt-8 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <h3 class="text-lg font-medium text-yellow-800 mb-2">Email Verification</h3>
                    <p class="text-yellow-700 mb-4">Your email address is not verified. Please verify your email to access all features.</p>
                    <form action="{{ route('verification.send') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-envelope mr-2"></i>Resend Verification Email
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Statistics Tab -->
        <div id="content-stats" class="tab-content space-y-6">
            <div class="border-b border-gray-200 pb-4 mb-4">
                <h2 class="text-xl font-bold text-gray-800 font-montserrat">Account Statistics</h2>
                <p class="text-gray-600 text-sm">Overview of your reservation activities</p>
            </div>

            @php
                $totalCount = $user->reservations()->count();
                $pendingCount = $user->reservations()->whereIn('status', ['pending','approved_IOSA','approved_mhadel'])->count();
                $approvedCount = $user->reservations()->whereIn('status', ['approved','approved_OTP'])->count();
                $rejectedCount = $user->reservations()->whereIn('status', ['rejected','rejected_OTP'])->count();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-100 stat-card">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-blue-600">{{ $totalCount }}</div>
                    <div class="text-sm text-gray-600">Total Reservations</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg border border-yellow-100 stat-card">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</div>
                    <div class="text-sm text-gray-600">Pending/In Review</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg border border-green-100 stat-card">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-green-600">{{ $approvedCount }}</div>
                    <div class="text-sm text-gray-600">Approved</div>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg border border-red-100 stat-card">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-red-600">{{ $rejectedCount }}</div>
                    <div class="text-sm text-gray-600">Rejected</div>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Recent Activity</h3>
                @php
                    $recentReservations = $user->reservations()->latest()->take(5)->get();
                @endphp

                @if($recentReservations->count() > 0)
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentReservations as $reservation)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $reservation->event_title }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $reservation->venue->name ?? 'No venue' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $reservation->start_date ? $reservation->start_date->format('M d, Y') : 'No date' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @switch($reservation->status)
                                                @case('pending')
                                                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                        <i class="fas fa-clock mr-1"></i> Pending
                                                    </span>
                                                    @break
                                                @case('approved')
                                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                        <i class="fas fa-check-circle mr-1"></i> Approved
                                                    </span>
                                                    @break
                                                @case('rejected')
                                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                        <i class="fas fa-times-circle mr-1"></i> Rejected
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                                        {{ ucfirst($reservation->status) }}
                                                    </span>
                                            @endswitch
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('user.reservations.index') }}" class="inline-block text-sm text-maroon hover:text-red-800">
                            View all reservations <i class="fas fa-chevron-right ml-1"></i>
                        </a>
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-600">No reservations yet</p>
                        <a href="{{ route('user.reservations.calendar') }}" class="inline-block mt-4 px-4 py-2 bg-maroon text-white rounded-lg hover:bg-red-800 transition-colors">
                            Make Your First Reservation
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 font-montserrat">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <a href="{{ route('user.reservations.calendar') }}" class="flex items-center p-3 bg-maroon text-white rounded-lg hover:bg-red-800 transition-colors action-card">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center mr-3">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <div>
                    <h3 class="font-medium">New Reservation</h3>
                    <p class="text-sm opacity-80">Book a venue for your event</p>
                </div>
            </a>
            
            <a href="{{ route('user.reservations.index') }}" class="flex items-center p-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors action-card">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center mr-3">
                    <i class="fas fa-book-open"></i>
                </div>
                <div>
                    <h3 class="font-medium">My Reservations</h3>
                    <p class="text-sm opacity-80">View your history and status</p>
                </div>
            </a>
            
            <a href="{{ route('user.reservations.calendar') }}" class="flex items-center p-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors action-card">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center mr-3">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h3 class="font-medium">Calendar View</h3>
                    <p class="text-sm opacity-80">See your scheduled events</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showTab(name) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(function(el) {
            el.classList.remove('active');
        });
        
        // Deactivate all tab buttons
        document.querySelectorAll('.tab-button').forEach(function(btn) {
            btn.classList.remove('active');
            btn.classList.add('text-gray-600');
        });
        
        // Show selected tab content and activate button
        const content = document.getElementById('content-' + name);
        const tab = document.getElementById('tab-' + name);
        
        if (content) {
            content.classList.add('active');
        }
        
        if (tab) {
            tab.classList.add('active');
            tab.classList.remove('text-gray-600');
        }
    }
    
    // Add hover effects to stat cards
    document.addEventListener('DOMContentLoaded', function() {
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.classList.add('transform', 'scale-105');
            });
            card.addEventListener('mouseleave', function() {
                this.classList.remove('transform', 'scale-105');
            });
        });
    });
</script>
@endsection