@extends('layouts.user')

@section('title', 'Access Diagnostic')
@section('page-title', 'Access Diagnostic')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Account Diagnostic</h2>
                <p class="text-gray-600 text-sm">Your current account information</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 font-medium">User ID:</span>
                    <span class="text-gray-800 font-mono">{{ auth()->id() }}</span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 font-medium">Name:</span>
                    <span class="text-gray-800">{{ auth()->user()->name }}</span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 font-medium">Email:</span>
                    <span class="text-gray-800">{{ auth()->user()->email }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 font-medium">Role:</span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        {{ strtolower(auth()->user()->role) === 'user' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ auth()->user()->role ?? 'No role assigned' }}
                    </span>
                </div>
            </div>

            @if(strtolower(auth()->user()->role ?? '') !== 'user')
            <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-red-800 font-bold mb-2">Role Mismatch Detected</h3>
                        <p class="text-red-700 text-sm mb-3">
                            Your account role is "<strong>{{ auth()->user()->role }}</strong>", but it should be "<strong>User</strong>" (case-sensitive) to access user pages.
                        </p>
                        <p class="text-red-700 text-sm">
                            Please contact an administrator to correct your role assignment.
                        </p>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-green-800 font-bold mb-2">Role OK</h3>
                        <p class="text-green-700 text-sm">
                            Your role is correctly set. You should have access to all user pages.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-blue-800 font-semibold mb-2">Troubleshooting Steps:</h4>
                <ol class="list-decimal list-inside space-y-2 text-blue-700 text-sm">
                    <li>If you see a 403 error, check if your role exactly matches "User"</li>
                    <li>Try logging out and logging back in</li>
                    <li>Clear your browser cache and cookies</li>
                    <li>If the problem persists, contact support with your User ID: <code class="bg-white px-2 py-1 rounded text-blue-900">{{ auth()->id() }}</code></li>
                </ol>
            </div>

            <div class="flex justify-center space-x-4">
                <a href="{{ route('user.dashboard') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-home mr-2"></i>Go to Dashboard
                </a>
                <a href="{{ route('user.reservations.index') }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-calendar mr-2"></i>View Reservations
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
