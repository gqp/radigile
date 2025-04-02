<?php


use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// use App\Http\Middleware\isAdminMiddleware;
// use App\Http\Middleware\isUserMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Authenticate::class;
        $middleware->alias([
            'auth' => Authenticate::class,
            'isAdmin' => App\Http\Middleware\isAdminMiddleware::class,
            'isUser' => App\Http\Middleware\isUserMiddleware::class,
            'preventBackHostory' => App\Http\Middleware\PreventBackHostoryMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
