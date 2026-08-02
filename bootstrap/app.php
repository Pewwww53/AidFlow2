<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->alias([
            'firebase.auth' => \App\Http\Middleware\FirebaseAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $exception): void {
            error_log((string) $exception);
        });

        $exceptions->render(function (\Throwable $exception) {
            return new \Symfony\Component\HttpFoundation\Response(
                (string) $exception,
                500,
                ['Content-Type' => 'text/plain']
            );
        });
    })->create();
