<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Facade;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register PDF facade alias
        $this->app->alias('pdf', Pdf::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $user = Auth::user();
            if ($user) {
                $unreadCount = Notification::where('user_id', $user->id)->whereNull('read_at')->count();
                $latestNotifications = Notification::where('user_id', $user->id)
                    ->latest()->take(5)->get();
                $view->with('globalUnreadNotifications', $unreadCount)
                     ->with('globalLatestNotifications', $latestNotifications);
            }
        });
    }
}
