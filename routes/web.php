<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\IOSA\IOSAController;
use App\Http\Controllers\IOSA\ReservationController as IOSAReservationController;
use App\Http\Controllers\IOSA\EventController as IOSAEventController;

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
use App\Http\Controllers\GSU\EventController as GSUEventController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Auth;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.submit');

// Password Reset Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    // Find the user by ID
    $user = \App\Models\User::findOrFail($id);
    
    // Verify the hash matches
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification link.');
    }
    
    // Check if already verified
    if ($user->hasVerifiedEmail()) {
        return redirect()->route('login')->with('message', 'Email already verified. Please log in.');
    }
    
    // Mark email as verified
    $user->markEmailAsVerified();
    
    // Fire the verified event
    event(new \Illuminate\Auth\Events\Verified($user));
    
    return redirect()->route('verification.success')->with('verified_name', $user->name);
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

Route::view('/email/verified-success', 'auth.verified-success')->name('verification.success');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Resend Verification Routes (without auth)
Route::get('/email/resend-verification', [AuthController::class, 'showResendVerification'])->name('verification.resend.form');
Route::post('/email/resend-verification', [AuthController::class, 'resendVerification'])->middleware('throttle:6,1')->name('verification.resend.send');

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
        Route::get('reservations/{id}/edit', [UserController::class, 'edit'])->name('reservations.edit');
        Route::put('reservations/{id}', [UserController::class, 'update'])->name('reservations.update');
        Route::delete('reservations/{id}', [UserController::class, 'cancel'])->name('reservations.cancel');
        
        // User Reservation Rating Routes
        Route::post('reservations/{reservation}/rate', [UserController::class, 'rateReservation'])->name('reservations.rate');
        Route::get('reservations/{reservation}/rating', [UserController::class, 'getReservationRating'])->name('reservations.rating');
    });
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::put('/password', [UserController::class, 'updatePassword'])->name('user.password.update');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Admin Routes
    Route::middleware(['auth', 'admin.role'])->prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::resource('venues', VenueController::class, ['as' => 'admin']);



        // Events Routes
        Route::resource('events', EventController::class, ['as' => 'admin']);
    });

    // IOSA Routes
    Route::group(['prefix' => 'iosa', 'as' => 'iosa.', 'middleware' => ['auth', 'iosa.role']], function () {
        Route::get('/', [IOSAController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports', [IOSAController::class, 'reports'])->name('reports');
        Route::get('/reports/{report}', [IOSAController::class, 'showReport'])->name('reports.show');
        Route::post('/reports/{report}/status', [IOSAController::class, 'updateReportStatus'])->name('reports.update-status');
        Route::get('/reports/export/excel', [IOSAController::class, 'exportGsuReports'])->name('reports.export');
        Route::get('/reservation-reports', [IOSAController::class, 'reservationReports'])->name('reservation-reports');
        Route::get('/reservation-reports-export', [IOSAController::class, 'exportReservationReports'])->name('reservation-reports.export');
        Route::get('/reservation-reports-export-pdf', [IOSAController::class, 'exportReservationReportsPdf'])->name('reservation-reports.exportPdf');
        
        // Reservations Routes
        Route::get('reservations/calendar', [IOSAReservationController::class, 'calendar'])->name('reservations.calendar');
        Route::resource('reservations', IOSAReservationController::class);
        Route::post('reservations/{id}/approve', [IOSAReservationController::class, 'approve'])->name('reservations.approve');
        Route::post('reservations/{id}/reject', [IOSAReservationController::class, 'reject'])->name('reservations.reject');
        Route::get('reservations/{id}/download-activity-grid', [IOSAReservationController::class, 'downloadActivityGrid'])->name('reservations.download-activity-grid');
        
        // IOSA Events Routes
        Route::get('events', [IOSAEventController::class, 'index'])->name('events.index');
        Route::get('events/create', [IOSAEventController::class, 'create'])->name('events.create');
        Route::post('events', [IOSAEventController::class, 'store'])->name('events.store');
        Route::get('events/{event}', [IOSAEventController::class, 'show'])->name('events.show');
        Route::get('events/{event}/edit', [IOSAEventController::class, 'edit'])->name('events.edit');
        Route::put('events/{event}', [IOSAEventController::class, 'update'])->name('events.update');
        Route::get('events-calendar', [IOSAEventController::class, 'calendar'])->name('events.calendar');
        Route::get('events-export', [IOSAEventController::class, 'export'])->name('events.export');
        Route::post('events/check-conflicts', [IOSAEventController::class, 'checkConflicts'])->name('events.check-conflicts');
        
        // IOSA Profile Routes
        Route::get('profile', [IOSAController::class, 'profile'])->name('profile');
        Route::put('profile', [IOSAController::class, 'updateProfile'])->name('profile.update');
        Route::put('password', [IOSAController::class, 'updatePassword'])->name('password.update');
    });

    // Mhadel Routes
    Route::group(['prefix' => 'mhadel', 'as' => 'mhadel.', 'middleware' => ['auth', 'mhadel.role']], function () {
        Route::get('/', [MhadelController::class, 'dashboard'])->name('dashboard');
        // Profile
        Route::get('/profile', [MhadelController::class, 'profile'])->name('profile');
        Route::put('/profile', [MhadelController::class, 'updateProfile'])->name('profile.update');
        Route::get('/reports', [MhadelController::class, 'reports'])->name('reports');
        Route::get('/reports/{report}', [MhadelController::class, 'showReport'])->name('reports.show');
        Route::get('/reports-export', [MhadelController::class, 'exportReports'])->name('reports.export');
        Route::get('/reports-export-pdf', [MhadelController::class, 'exportReportsPdf'])->name('reports.exportPdf');
        Route::get('/gsu-reports', [MhadelController::class, 'gsuReports'])->name('gsu-reports');
        Route::get('/gsu-reports/{report}', [MhadelController::class, 'showGsuReport'])->name('gsu-reports.show');
        Route::post('/gsu-reports/{report}/status', [MhadelController::class, 'updateGsuReportStatus'])->name('gsu-reports.update-status');
        Route::get('/gsu-reports/export/excel', [MhadelController::class, 'exportGsuReports'])->name('gsu-reports.export');
        
        // Venues Routes
        Route::resource('venues', MhadelVenueController::class);
        
        // Events Routes
        Route::resource('events', MhadelEventController::class);
        Route::get('events-export', [MhadelEventController::class, 'export'])->name('events.export');
        Route::post('events/{event}/cancel', [MhadelEventController::class, 'cancel'])->name('events.cancel');
        Route::post('events/{event}/complete', [MhadelEventController::class, 'markAsComplete'])->name('events.complete');
        Route::post('events/update-statuses', [MhadelEventController::class, 'updateStatuses'])->name('events.update-statuses');
        Route::post('events/{event}/update-schedule', [MhadelEventController::class, 'updateSchedule'])->name('events.update-schedule');
        Route::post('events/check-conflicts', [MhadelEventController::class, 'checkConflicts'])->name('events.check-conflicts');
        Route::post('events/{event}/check-conflicts', [MhadelEventController::class, 'checkConflicts'])->name('events.check-conflicts-edit');
        
        // Reservations Routes
        Route::get('reservations/calendar', [MhadelReservationController::class, 'calendar'])->name('reservations.calendar');
        Route::resource('reservations', MhadelReservationController::class);
        Route::get('reservations/{id}/edit', [MhadelReservationController::class, 'edit'])->name('reservations.edit');
        Route::post('reservations/{id}/approve', [MhadelReservationController::class, 'approve'])->name('reservations.approve');
        Route::post('reservations/{id}/reject', [MhadelReservationController::class, 'reject'])->name('reservations.reject');
        Route::get('reservations/{id}/download-activity-grid', [MhadelReservationController::class, 'downloadActivityGrid'])->name('reservations.download-activity-grid');
        Route::post('reservations/{id}/update-schedule', [MhadelReservationController::class, 'updateSchedule'])->name('reservations.update-schedule');
        Route::post('reservations/{id}/check-conflicts', [MhadelReservationController::class, 'checkConflicts'])->name('reservations.check-conflicts');
    });



    // OTP Routes
    Route::group(['prefix' => 'drjavier', 'as' => 'drjavier.', 'middleware' => ['auth', 'otp.role']], function () {
        Route::get('/', [DrJavierController::class, 'dashboard'])->name('dashboard');
        Route::get('/gsu-reports', [OTPReservationController::class, 'gsuReports'])->name('gsu-reports');
        Route::get('/gsu-reports/{report}', [OTPReservationController::class, 'showGsuReport'])->name('gsu-reports.show');
        Route::post('/gsu-reports/{report}/status', [OTPReservationController::class, 'updateGsuReportStatus'])->name('gsu-reports.update-status');
        Route::get('/gsu-reports/export/excel', [DrJavierController::class, 'exportGsuReports'])->name('gsu-reports.export');
        Route::get('/reports/reservation-reports', [DrJavierController::class, 'reservationReports'])->name('reports.reservation-reports');
        Route::get('/reports/reservation-reports/export', [DrJavierController::class, 'exportReports'])->name('reports.reservation-reports.export');
        Route::get('/reports/reservation-reports/export-pdf', [DrJavierController::class, 'exportReportsPdf'])->name('reports.reservation-reports.exportPdf');
        Route::get('/profile', [DrJavierController::class, 'profile'])->name('profile');
        Route::post('/profile', [DrJavierController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/password', [DrJavierController::class, 'updatePassword'])->name('profile.password');
        Route::resource('reservations', OTPReservationController::class);
        Route::post('reservations/{id}/approve', [OTPReservationController::class, 'approve'])->name('reservations.approve');
        Route::post('reservations/{id}/reject', [OTPReservationController::class, 'reject'])->name('reservations.reject');
        Route::get('reservations/{id}/download-activity-grid', [OTPReservationController::class, 'downloadActivityGrid'])->name('reservations.download-activity-grid');
        // Removed export route for Dr. Javier reservations
        
        // Dr. Javier Events Routes
        Route::get('events', [\App\Http\Controllers\DrJavier\EventController::class, 'index'])->name('events.index');
        Route::get('events/{event}', [\App\Http\Controllers\DrJavier\EventController::class, 'show'])->name('events.show');
        Route::get('events-export', [\App\Http\Controllers\DrJavier\EventController::class, 'export'])->name('events.export');
        Route::get('events-calendar', [\App\Http\Controllers\DrJavier\EventController::class, 'calendar'])->name('events.calendar');
    });

    // GSU Routes
    Route::group(['prefix' => 'gsu', 'as' => 'gsu.', 'middleware' => ['auth', 'gsu.role']], function () {
        Route::get('/dashboard', [GSUController::class, 'dashboard'])->name('dashboard');
        
        // GSU Profile Routes
        Route::get('profile', [GSUController::class, 'profile'])->name('profile');
        Route::put('profile', [GSUController::class, 'updateProfile'])->name('profile.update');
        
        Route::resource('reservations', GSUReservationController::class);
        Route::get('reservations/{id}/pdf', [GSUReservationController::class, 'pdf'])->name('reservations.pdf');
        Route::get('reservations-export', [GSUReservationController::class, 'export'])->name('reservations.export');
        Route::post('reservations/{id}/complete', [GSUReservationController::class, 'markAsComplete'])->name('reservations.complete');
        Route::post('reservations/{id}/report', [GSUReservationController::class, 'reportIssue'])->name('reservations.report');
        
        // GSU Events Routes
        Route::get('events', [GSUEventController::class, 'index'])->name('events.index');
        Route::get('events/{event}', [GSUEventController::class, 'show'])->name('events.show');
        Route::get('events-export', [GSUEventController::class, 'export'])->name('events.export');
        Route::post('events/{event}/complete', [GSUEventController::class, 'markAsComplete'])->name('events.complete');
        Route::post('events/{event}/report', [GSUEventController::class, 'reportIssue'])->name('events.report');
        Route::post('events/update-statuses', [GSUEventController::class, 'updateStatuses'])->name('events.update-statuses');
        
        // GSU Calendar Route
        Route::get('calendar', [GSUEventController::class, 'calendar'])->name('calendar');
        
        // GSU Reports Routes
        Route::get('reports', [GSUController::class, 'reports'])->name('reports');
        Route::get('reports-export', [GSUController::class, 'exportReports'])->name('reports.export');
        Route::get('reports-export-pdf', [GSUController::class, 'exportReportsPdf'])->name('reports.exportPdf');
    });

    // Other user routes defined here
});
