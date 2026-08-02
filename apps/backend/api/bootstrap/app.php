<?php

use App\Http\Middleware\EnsureSuperadmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'superadmin' => EnsureSuperadmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

$centralEnvironmentPath = dirname(__DIR__, 4).'/env';

if (is_file($centralEnvironmentPath.'/api.env')) {
    $app->useEnvironmentPath($centralEnvironmentPath);
    $app->loadEnvironmentFrom('api.env');
}

return $app;
