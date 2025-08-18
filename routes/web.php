<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\IOSA\IOSAController;
use App\Http\Controllers\IOSA\ReservationController as IOSAReservationController;

use App\Http\Controllers\Admin\EventController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Mhadel\MhadelController;
use App\Http\Controllers\Mhadel\ReservationController as MhadelReservationController;
use App\Http\Controllers\Mhadel\VenueController as MhadelVenueController;
use App\Http\Controllers\Mhadel\EventController as MhadelEventController;
use App\Http\Controllers\DrJavier\DrJavierController;
use App\Http\Controllers\DrJavier\ReservationController as OTPReservationController;
use App\Http\Controllers\GSU\GSUController as GSUController;
use App\Http\Controllers\GSU\ReservationController as GSUReservationController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Protected Routes
Route::middleware(['auth', 'verified'])->group(function () {

    // User Routes
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::middleware(['auth', 'user.role'])->prefix('user')->name('user.')->group(function () {
        Route::get('reservations', [UserController::class, 'index'])->name('reservations.index');
        Route::get('reservations/calendar', [UserController::class, 'calendar'])->name('reservations.calendar');
        Route::post('reservations', [UserController::class, 'storeReservation'])->name('reservations.store');
        Route::get('reservations/unavailable', [UserController::class, 'unavailable'])->name('reservations.unavailable');
        Route::get('reservations/{id}', [UserController::class, 'show'])->name('reservations.show');
    });
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');

    // Admin Routes
    Route::middleware(['auth', 'admin.role'])->prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::resource('venues', VenueController::class, ['as' => 'admin']);



        // Events Routes
        Route::resource('events', EventController::class, ['as' => 'admin']);
    });

    // IOSA Routes
    Route::middleware(['auth', 'iosa.role'])->prefix('iosa')->group(function () {
        Route::get('/', [IOSAController::class, 'dashboard'])->name('iosa.dashboard');
        
        // Reservations Routes
        Route::resource('reservations', IOSAReservationController::class, ['as' => 'iosa']);
        Route::post('reservations/{id}/approve', [IOSAReservationController::class, 'approve'])->name('iosa.reservations.approve');
        Route::post('reservations/{id}/reject', [IOSAReservationController::class, 'reject'])->name('iosa.reservations.reject');
        Route::get('reservations/{id}/download-activity-grid', [IOSAReservationController::class, 'downloadActivityGrid'])->name('iosa.reservations.download-activity-grid');
    });

    // Mhadel Routes
    Route::group(['prefix' => 'mhadel', 'as' => 'mhadel.', 'middleware' => ['auth', 'mhadel.role']], function () {
        Route::get('/dashboard', [MhadelController::class, 'dashboard'])->name('dashboard');
        
        // Venues Routes
        Route::resource('venues', MhadelVenueController::class);
        
        // Events Routes
        Route::resource('events', MhadelEventController::class);
        
        // Reservations Routes
        Route::get('reservations/calendar', [MhadelReservationController::class, 'calendar'])->name('reservations.calendar');
        Route::resource('reservations', MhadelReservationController::class);
        Route::post('reservations/{id}/approve', [MhadelReservationController::class, 'approve'])->name('reservations.approve');
        Route::post('reservations/{id}/reject', [MhadelReservationController::class, 'reject'])->name('reservations.reject');
        Route::get('reservations/{id}/download-activity-grid', [MhadelReservationController::class, 'downloadActivityGrid'])->name('reservations.download-activity-grid');
    });

    // OTP Routes
    Route::group(['prefix' => 'drjavier', 'as' => 'drjavier.', 'middleware' => ['auth', 'otp.role']], function () {
        Route::get('/dashboard', [DrJavierController::class, 'dashboard'])->name('dashboard');
        
        // Reservations Routes
        Route::resource('reservations', OTPReservationController::class);
        Route::post('reservations/{id}/approve', [OTPReservationController::class, 'approve'])->name('reservations.approve');
        Route::post('reservations/{id}/reject', [OTPReservationController::class, 'reject'])->name('reservations.reject');
        Route::get('reservations/{id}/download-activity-grid', [OTPReservationController::class, 'downloadActivityGrid'])->name('reservations.download-activity-grid');
        Route::get('reservations-export', [OTPReservationController::class, 'export'])->name('reservations.export');
    });

    // GSU Routes
    Route::group(['prefix' => 'gsu', 'as' => 'gsu.', 'middleware' => ['auth', 'gsu.role']], function () {
        Route::get('/dashboard', [GSUController::class, 'dashboard'])->name('dashboard');
        Route::resource('reservations', GSUReservationController::class);
    });

    // Other user routes defined here
});
