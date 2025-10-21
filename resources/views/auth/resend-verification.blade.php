<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Verification Email - PCC Venue Reservation System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .bg-maroon {
            background-color: #8B1818;
        }
        .btn-maroon {
            background-color: #8B1818;
            color: white;
        }
        .btn-maroon:hover {
            background-color: #6B1212;
        }
        .text-maroon {
            color: #8B1818;
        }
        .login-bg {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .card-shadow {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="min-h-screen login-bg flex items-center justify-center p-8">
    <div class="w-full max-w-md">
        <!-- Resend Verification Card -->
        <div class="bg-white rounded-2xl card-shadow p-8">
            <!-- Icon and Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                    <i class="fas fa-envelope-open-text text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Resend Verification Email</h3>
                <p class="text-gray-600">Enter your email address to receive a new verification link</p>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        <p class="text-green-700 text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                        <p class="text-red-700 text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Resend Form -->
            <form action="{{ route('verification.resend.send') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-maroon"></i>Email Address
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        value="{{ old('email', $email ?? '') }}"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-maroon focus:ring-2 focus:ring-maroon focus:ring-opacity-20 transition-all duration-300"
                        placeholder="Enter your email address"
                    >
                    @error('email')
                        <p class="text-red-500 text-sm mt-2 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Submit Button -->
                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full btn-maroon py-3 px-6 rounded-lg font-semibold text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-maroon focus:ring-opacity-20"
                    >
                        <i class="fas fa-paper-plane mr-2"></i>Send Verification Email
                    </button>
                </div>
            </form>

            <!-- Back to Login -->
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-maroon text-sm font-medium hover:underline inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Login
                </a>
            </div>

            <!-- Information Box -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                        <div class="text-sm text-gray-700">
                            <p class="font-semibold mb-1">What happens next?</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Check your email inbox (and spam folder)</li>
                                <li>Click the verification link in the email</li>
                                <li>Return to login page and sign in</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Auto-focus on email field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });

        // Add loading animation to submit button
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
