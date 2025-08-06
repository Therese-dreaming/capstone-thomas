<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - PCC</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
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
        .logo-glow {
            filter: drop-shadow(0 0 15px rgba(255, 255, 255, 0.3));
        }
    </style>
</head>
<body class="h-full">
    <div class="flex h-full">
        <!-- Left side with logo -->
        <div class="w-1/2 bg-maroon flex items-center justify-center">
            <div class="text-center">
                <img src="{{ asset('images/pcclogo.png') }}" alt="PCC Logo" class="w-100 h-100 mx-auto logo-glow">
            </div>
        </div>
        
        <!-- Right side with form -->
        <div class="w-1/2 bg-white bg-opacity-90 flex items-center justify-center" 
             style="background-image: url('{{ asset('venue/pcc-building.jpg') }}'); background-size: cover; background-blend-mode: lighten;">
            <div class="w-4/5 max-w-md">
                <h2 class="text-3xl font-bold text-maroon mb-8 text-center">Sign Up</h2>
                
                <form action="{{ route('signup.submit') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" id="first_name" name="first_name" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-maroon focus:border-maroon">
                        @error('first_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-maroon focus:border-maroon">
                        @error('last_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-maroon focus:border-maroon">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" id="password" name="password" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-maroon focus:border-maroon">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Retype Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-maroon focus:border-maroon">
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700">Department/Organization</label>
                        <input type="text" id="department" name="department" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-maroon focus:border-maroon"
                            placeholder="Enter your department or organization">
                        @error('department')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="w-full btn-maroon py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon">
                            Sign Up
                        </button>
                    </div>
                </form>
                
                <div class="text-center text-sm text-gray-600 mt-4">
                    Already have an account? <a href="{{ route('login') }}" class="font-medium text-maroon hover:text-red-800">Login</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>