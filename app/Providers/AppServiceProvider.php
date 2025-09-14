<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Facade;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use App\Models\Notification;
use App\Models\Reservation;
use App\Models\Event;
use App\Models\Report;

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
        // Share notifications with all views
        View::composer('*', function ($view) {
            $user = Auth::user();
            if ($user) {
                // Clear the cache first to ensure we get fresh data
                Cache::forget('notifications.' . $user->id);
                Cache::forget('latest_notifications.' . $user->id);
                
                $unreadCount = Cache::remember('notifications.' . $user->id, 300, function () use ($user) {
                    return Notification::where('user_id', $user->id)
                        ->whereNull('read_at')
                        ->count();
                });
                
                $latestNotifications = Cache::remember('latest_notifications.' . $user->id, 300, function () use ($user) {
                    return Notification::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->whereNull('read_at')
                        ->take(5)
                        ->get();
                });

                $view->with([
                    'globalUnreadNotifications' => $unreadCount,
                    'globalLatestNotifications' => $latestNotifications
                ]);

                // Debug log to check notifications
                \Log::info('Notifications Debug', [
                    'user_id' => $user->id,
                    'unread_count' => $unreadCount,
                    'latest_notifications' => $latestNotifications->toArray()
                ]);
            }
        });

        // Share sidebar counts with Mhadel layout
        View::composer('layouts.mhadel', \App\View\Composers\MhadelSidebarComposer::class);
        
        // Share sidebar counts with GSU layout
        View::composer('layouts.gsu', \App\View\Composers\GSUSidebarComposer::class);
        
        // Share sidebar counts with IOSA layout
        View::composer('layouts.iosa', \App\View\Composers\IosaSidebarComposer::class);
        
        // Share sidebar counts with DrJavier layout
        View::composer('layouts.drjavier', \App\View\Composers\DrJavierSidebarComposer::class);
    }
}
