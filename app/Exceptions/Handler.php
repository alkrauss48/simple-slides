<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // An expired invitation is an ordinary thing to hit, not an error the
        // recipient can act on from a bare "Invalid signature." 403.
        $this->renderable(function (InvalidSignatureException $e, Request $request): ?Response {
            if (! $request->routeIs('invitations.*')) {
                return null;
            }

            return response()->view('errors.invitation-invalid', status: 403);
        });
    }
}
