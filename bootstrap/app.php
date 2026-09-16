<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Carried over from the deleted App\Http\Middleware\TrustProxies.
        $middleware->trustProxies(at: '*');

        // AddLinkHeadersForPreloadedAssets was in Laravel 10's default web
        // group but is opt-in from 11 onwards, so it has to be restated or the
        // app quietly stops sending preload Link headers for its Vite assets.
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // An expired invitation is an ordinary thing to hit, not an error the
        // recipient can act on from a bare "Invalid signature." 403.
        $exceptions->render(function (InvalidSignatureException $e, Request $request): ?Response {
            if (! $request->routeIs('invitations.*')) {
                return null;
            }

            return response()->view('errors.invitation-invalid', status: 403);
        });
    })->create();
