@extends('layouts.user')

@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Profile Information -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Profile Information</h2>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-maroon rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-800">{{ $user->name }}</h3>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    <p class="text-sm text-gray-500">Member since {{ $user->created_at->format('M Y') }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <p class="text-gray-800">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <p class="text-gray-800">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <p class="text-gray-800">{{ ucfirst($user->role ?? 'user') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Verified</label>
                    <p class="text-gray-800">
                        @if($user->email_verified_at)
                            <span class="text-green-600">✓ Verified</span>
                        @else
                            <span class="text-red-600">✗ Not verified</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Statistics -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Account Statistics</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">
                        {{ $user->reservations()->count() }}
                    </div>
                    <div class="text-sm text-gray-600">Total Reservations</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">
                        {{ $user->reservations()->where('status', 'approved')->count() }}
                    </div>
                    <div class="text-sm text-gray-600">Approved</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">
                        {{ $user->reservations()->where('status', 'pending')->count() }}
                    </div>
                    <div class="text-sm text-gray-600">Pending</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Quick Actions</h2>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <a href="{{ route('user.reservations.index') }}" class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-maroon hover:bg-maroon hover:text-white transition-colors">
                    <i class="fas fa-calendar-plus text-xl mr-3"></i>
                    <div>
                        <h3 class="font-medium">Make New Reservation</h3>
                        <p class="text-sm opacity-75">Book a venue for your event</p>
                    </div>
                </a>
                
                @if(!$user->email_verified_at)
                <form action="{{ route('verification.send') }}" method="POST" class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-maroon hover:bg-maroon hover:text-white transition-colors">
                    @csrf
                    <button type="submit" class="flex items-center w-full text-left">
                        <i class="fas fa-envelope text-xl mr-3"></i>
                        <div>
                            <h3 class="font-medium">Resend Verification Email</h3>
                            <p class="text-sm opacity-75">Verify your email address</p>
                        </div>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 