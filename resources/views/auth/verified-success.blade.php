<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Email Verified - PCC Venue Reservation</title>
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
		.login-bg {
			background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
		}
		.card-shadow {
			box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
		}
		.success-animation {
			animation: scaleIn 0.5s ease-out;
		}
		@keyframes scaleIn {
			0% { transform: scale(0); opacity: 0; }
			50% { transform: scale(1.1); }
			100% { transform: scale(1); opacity: 1; }
		}
	</style>
</head>
<body class="min-h-screen login-bg flex items-center justify-center p-8">
	<div class="bg-white rounded-2xl card-shadow p-8 w-full max-w-md text-center">
		<div class="mx-auto w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mb-6 success-animation">
			<i class="fas fa-check-circle text-green-600 text-4xl"></i>
		</div>
		<h1 class="text-3xl font-bold text-gray-800 mb-3">Email Verified!</h1>
		<p class="text-gray-600 text-lg mb-2">
			Thank you{{ session('verified_name') ? ', ' . session('verified_name') : '' }}!
		</p>
		<p class="text-gray-600 mb-6">Your email has been verified successfully. You can now log in to your account.</p>
		
		<!-- Success Info Box -->
		<div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
			<div class="flex items-center justify-center text-sm text-green-700">
				<i class="fas fa-info-circle mr-2"></i>
				<span>Your account is now fully activated</span>
			</div>
		</div>

		<!-- Proceed to Login Button -->
		<a href="{{ route('login') }}" class="inline-block w-full btn-maroon py-3 px-6 rounded-lg font-semibold text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-maroon focus:ring-opacity-20">
			<i class="fas fa-sign-in-alt mr-2"></i>Proceed to Login
		</a>

		<!-- Additional Info -->
		<div class="mt-6 pt-6 border-t border-gray-200">
			<p class="text-xs text-gray-500">
				<i class="fas fa-shield-alt mr-1"></i>
				Your account is secure and ready to use
			</p>
		</div>
	</div>
</body>
</html> 