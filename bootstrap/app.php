<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            if (! $request->isMethod('post') || $exception->getStatusCode() !== 419) {
                return null;
            }

            $loginRoute = match ($request->path()) {
                'admin-login' => 'admin.login',
                'kurir-login' => 'kurir.login',
                'login' => 'login',
                default => null,
            };

            if (! $loginRoute) {
                return null;
            }

            return redirect()
                ->route($loginRoute)
                ->with('error', 'Sesi login sudah kedaluwarsa. Silakan masuk kembali.');
        });
    })->create();