<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/web.php',
            __DIR__.'/../routes/admin.php',
            __DIR__.'/../routes/partner.php',
            __DIR__.'/../routes/front.php',
        ],
        api: [
            __DIR__.'/../routes/api/admin.php',
            __DIR__.'/../routes/api/partner.php',
            __DIR__.'/../routes/api/front.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.client' => \App\Http\Middleware\ApiClientAuth::class,
            'resolve.partner' => \App\Http\Middleware\ResolvePartner::class,
            'require.partner' => \App\Http\Middleware\RequirePartner::class,
            'idempotency' => \App\Http\Middleware\EnsureIdempotency::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
