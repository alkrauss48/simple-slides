{{--
    Shown when the `signed` middleware rejects an invitation URL. Self-contained
    like welcome.blade.php: the Inertia bundle is not loaded for error responses,
    and Filament's panel CSS is not in scope out here either.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Invitation link expired</title>
        <style>
            :root { color-scheme: light dark; }
            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.5rem;
                font-family: ui-sans-serif, system-ui, sans-serif;
                line-height: 1.6;
                background: #f3f4f6;
                color: #111827;
            }
            main { max-width: 32rem; text-align: center; }
            h1 { font-size: 1.5rem; margin: 0 0 0.75rem; }
            p { margin: 0 0 1rem; color: #4b5563; }
            a { color: inherit; }
            @media (prefers-color-scheme: dark) {
                body { background: #111827; color: #f9fafb; }
                p { color: #9ca3af; }
            }
        </style>
    </head>
    <body>
        <main>
            <h1>This invitation link is no longer valid</h1>
            <p>
                Invitation links expire after 7 days, and they stop working if the
                address gets altered along the way.
            </p>
            <p>
                Ask whoever invited you to send a fresh invitation, then open the
                link straight from that email.
            </p>
            <p><a href="{{ url('/') }}">Back to {{ config('app.name') }}</a></p>
        </main>
    </body>
</html>
