<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Simple Slides') }}</title>

        <meta name="description"
            content="{{ (isset($page['props']['meta']['description'])) ? $page['props']['meta']['description'] : config('meta.base_description') }}"/>
        <meta property="og:title"
            content="{{ (isset($page['props']['meta']['title'])) ? ($page['props']['meta']['title'] . ' - ' . config('app.name', 'Simple Slides')) : config('app.name', 'Simple Slides') }}"/>
        <meta property="og:description"
            content="{{ (isset($page['props']['meta']['description'])) ? $page['props']['meta']['description'] : config('meta.base_description') }}"/>
        <meta property="og:image"
            content="{{ (isset($page['props']['meta']['imageUrl'])) && !empty($page['props']['meta']['imageUrl']) ? $page['props']['meta']['imageUrl'] : config('meta.base_og_image') }}"/>
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:type" content="website">

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
        <link rel="manifest" href="/favicon/site.webmanifest">
        <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
        <link rel="shortcut icon" href="/favicon/favicon.ico">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="msapplication-config" content="/favicon/browserconfig.xml">
        <meta name="theme-color" content="#ffffff">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="{{ config('meta.twitter_handle') }}">
        <meta name="twitter:creator" content="{{ config('meta.twitter_handle') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>
