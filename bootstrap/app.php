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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'inactivite' => \App\Http\Middleware\VerifierInactivite::class,
            'module.actif' => \App\Http\Middleware\EnsureModuleActif::class,
            'tenant.actif' => \App\Http\Middleware\EnsureTenantActif::class,
        ]);

        // Deux guards, deux pages de connexion : /admin/* (espace éditeur, guard `admin`) doit
        // rediriger vers central.login, tout le reste (PME, guard `web`) vers login (tenant).
        $middleware->redirectGuestsTo(fn ($request) => $request->is('admin', 'admin/*')
            ? route('central.login')
            : route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
