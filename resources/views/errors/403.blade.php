<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Forbidden</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .maroon { color: #8b0000; }
        .bg-maroon { background-color: #8b0000; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-maroon text-white p-8 text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white bg-opacity-20 rounded-full mb-4">
                    <i class="fas fa-ban text-5xl"></i>
                </div>
                <h1 class="text-4xl font-bold mb-2">403</h1>
                <p class="text-xl">Access Forbidden</p>
            </div>

            <!-- Content -->
            <div class="p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-3">You don't have permission to access this page</h2>
                    
                    @if(isset($exception) && $exception->getMessage())
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-red-500"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-800">
                                        <strong>Reason:</strong> {{ $exception->getMessage() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <p class="text-gray-600 mb-4">
                        This could be because:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 mb-6">
                        <li>Your account role doesn't have access to this resource</li>
                        <li>You're trying to access someone else's reservation</li>
                        <li>Your session has expired</li>
                        <li>Your account permissions have changed</li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    @auth
                        <a href="{{ route('user.diagnostic') }}" class="block w-full text-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-stethoscope mr-2"></i>Check Account Diagnostic
                        </a>
                        <a href="{{ route('user.dashboard') }}" class="block w-full text-center px-6 py-3 bg-maroon text-white rounded-lg hover:bg-red-800 transition-colors">
                            <i class="fas fa-home mr-2"></i>Go to Dashboard
                        </a>
                        <a href="{{ route('user.reservations.index') }}" class="block w-full text-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-calendar mr-2"></i>My Reservations
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center px-6 py-3 bg-maroon text-white rounded-lg hover:bg-red-800 transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                    @endauth
                </div>

                <!-- Support Info -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 text-center">
                        <i class="fas fa-question-circle mr-1"></i>
                        If you believe this is an error, please contact support
                        @auth
                            with your User ID: <code class="bg-gray-100 px-2 py-1 rounded text-gray-800">{{ auth()->id() }}</code>
                        @endauth
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
