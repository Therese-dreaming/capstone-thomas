<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Verify Your Email - PCC Venue Reservation</title>
	<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">
	<div class="bg-white rounded-xl shadow-md p-8 w-full max-w-md text-center">
		<div class="mx-auto w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center mb-4">
			<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
				<path fill-rule="evenodd" d="M18 10A8 8 0 11.001 10 8 8 0 0118 10zM9 4a1 1 0 012 0v5a1 1 0 01-2 0V4zm1 9a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd" />
			</svg>
		</div>
		<h1 class="text-2xl font-bold text-gray-800">Please Verify Your Email</h1>
		<p class="text-gray-600 mt-2">We sent a verification link to your email address.</p>
		@if(session('message'))
			<p class="text-green-600 mt-2">{{ session('message') }}</p>
		@endif
		<form method="POST" action="{{ route('verification.send') }}" class="mt-6">
			@csrf
			<button type="submit" class="w-full px-6 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800">Resend Verification Email</button>
		</form>
		<form method="POST" action="{{ route('logout') }}" class="mt-3">
			@csrf
			<button type="submit" class="w-full px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Back to Login</button>
		</form>
	</div>
</body>
</html> 