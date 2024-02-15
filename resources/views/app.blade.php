<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Simple Slides') }}</title>
        <meta name="description" content="{{ config('meta.base_description') }}"/>
        <meta property="og:description" content="{{ config('meta.base_description') }}"/>
        <meta property="og:image" content="{{ config('meta.base_og_image') }}"/>

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

        <meta property="og:url" content="{{ config('app.url') }}">
        <meta property="og:type" content="website">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="{{ config('meta.twitter_handle') }}">
        <meta name="twitter:creator" content="{{ config('meta.twitter_handle') }}">

        @if(isset($page['props']['meta']))
            <title inertia>{{ (isset($page['props']['meta']['title'])) ? ($page['props']['meta']['title'] . ' - ' . config('app.name', 'Simple Slides')) : config('app.name', 'Simple Slides') }}</title>
            <meta property="og:title"
                content="{{ (isset($page['props']['meta']['title'])) ? ($page['props']['meta']['title'] . ' - ' . config('app.name', 'Simple Slides')) : config('app.name', 'Simple Slides') }}"/>
            <meta property="og:description"
                content="{{ (isset($page['props']['meta']['description'])) ? $page['props']['meta']['description'] : config('meta.base_description') }}"/>
            <meta property="og:image"
                content="{{ (isset($page['props']['meta']['imageUrl'])) ? $page['props']['meta']['imageUrl'] : config('meta.base_og_image') }}"/>
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>
