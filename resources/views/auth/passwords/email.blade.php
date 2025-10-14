<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - PCC Venue Reservation System</title>
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
        .border-maroon {
            border-color: #8B1818;
        }
        .focus\:ring-maroon:focus {
            --tw-ring-color: #8B1818;
        }
        .focus\:border-maroon:focus {
            border-color: #8B1818;
        }
        .logo-glow {
            filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.4));
        }
        .login-bg {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .card-shadow {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .input-focus:focus {
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        .bounce-in {
            animation: bounceIn 0.8s ease-out;
        }
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        .slide-in-right {
            animation: slideInRight 0.6s ease-out;
        }
        @keyframes slideInRight {
            0% { transform: translateX(100%); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body class="min-h-screen flex">
    <!-- Left Column - Logo and Branding -->
    <div class="w-1/2 bg-maroon flex flex-col items-center justify-center p-8">
        <div class="text-center bounce-in">
            <!-- Logo -->
            <div class="mb-8">
                <img src="{{ asset('images/pcclogo.png') }}" alt="PCC Logo" class="w-32 h-32 mx-auto logo-glow">
            </div>
            
            <!-- Institution Name -->
            <h1 class="text-white text-3xl font-bold mb-4">
                Pasig Catholic College
            </h1>
            
            <!-- System Title -->
            <h2 class="text-white text-xl mb-8 opacity-90">
                Venue Reservation System
            </h2>
            
            <!-- Welcome Message -->
            <div class="bg-white bg-opacity-10 rounded-lg p-6 backdrop-blur-sm">
                <p class="text-white text-lg font-medium mb-2">
                    <i class="fas fa-key mr-2"></i>Password Reset
                </p>
                <p class="text-white text-sm opacity-80">
                    Enter your email address and we'll send you a link to reset your password
                </p>
            </div>
        </div>
    </div>

    <!-- Right Column - Reset Form -->
    <div class="w-1/2 login-bg flex items-center justify-center p-8">
        <div class="w-full max-w-md slide-in-right">
            <!-- Reset Card -->
            <div class="bg-white rounded-2xl card-shadow p-8">
                <!-- Form Header -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-maroon rounded-full mb-4">
                        <i class="fas fa-lock text-white text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Forgot Password?</h3>
                    <p class="text-gray-600">No worries, we'll send you reset instructions</p>
                </div>

                <!-- Success Message -->
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <p class="text-green-700 text-sm">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Reset Form -->
                <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
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
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-maroon focus:ring-2 focus:ring-maroon focus:ring-opacity-20 input-focus transition-all duration-300"
                            placeholder="Enter your registered email"
                        >
                        @error('email')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button 
                            type="submit" 
                            class="w-full btn-maroon py-3 px-6 rounded-lg font-semibold text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-maroon focus:ring-opacity-20"
                        >
                            <i class="fas fa-paper-plane mr-2"></i>Send Reset Link
                        </button>
                    </div>
                    
                    <!-- Back to Login -->
                    <div class="text-center mt-6">
                        <a href="{{ route('login') }}" class="text-gray-600 text-sm hover:text-maroon transition-colors duration-200 inline-flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Login
                        </a>
                    </div>
                </form>

                <!-- System Information -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-xs text-gray-500 mb-2">
                            For assistance, contact your system administrator
                        </p>
                        <div class="flex justify-center space-x-4 text-xs text-gray-400">
                            <span>Version 1.0</span>
                            <span>•</span>
                            <span>&copy; {{ date('Y') }} PCC</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Add loading animation to submit button
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
            submitBtn.disabled = true;
        });

        // Auto-focus on email field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });
    </script>
</body>
</html>
