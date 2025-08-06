<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'user.role' => \App\Http\Middleware\UserRoleMiddleware::class,
            'admin.role' => \App\Http\Middleware\AdminRoleMiddleware::class,
            'iosa.role' => \App\Http\Middleware\IOSARoleMiddleware::class,
            'mhadel.role' => \App\Http\Middleware\MhadelRoleMiddleware::class,
            'drjavier.role' => \App\Http\Middleware\DrJavierRoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
