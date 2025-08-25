<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Email Verified - PCC Venue Reservation</title>
	<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">
	<div class="bg-white rounded-xl shadow-md p-8 w-full max-w-md text-center">
		<div class="mx-auto w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mb-4">
			<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" viewBox="0 0 20 20" fill="currentColor">
				<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
			</svg>
		</div>
		<h1 class="text-2xl font-bold text-gray-800">Email Verified</h1>
		<p class="text-gray-600 mt-2">Thank you{{ session('verified_name') ? ', ' . session('verified_name') : '' }}. Your email has been verified successfully.</p>
		<p class="text-gray-600 mt-1">You can now log in to your account.</p>
		<a href="{{ route('login') }}" class="inline-block mt-6 px-6 py-2 bg-maroon text-white rounded-lg hover:bg-red-700">Go to Login</a>
	</div>
</body>
</html> 