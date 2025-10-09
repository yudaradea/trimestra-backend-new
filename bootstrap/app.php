<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\UpdateTrimester;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Routing\Middleware\SubstituteBindings;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureAdmin::class,
            'api' => SubstituteBindings::class,
            'update-trimester' => UpdateTrimester::class,
        ]);

        $middleware->append(HandleCors::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        });
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command('reminder:daily')->dailyAt('08:00');
    })
    ->create();
