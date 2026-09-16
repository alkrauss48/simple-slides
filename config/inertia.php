<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    |
    | Inertia v3 defaults `paths` to `resources/js/pages`, but this application
    | capitalizes its frontend directories (`Pages`, `Components`, `Layouts`),
    | matching the glob in `resources/js/app.ts`. Without this override the
    | view finder misses every page on a case-sensitive filesystem, so
    | `assertInertia` passes on macOS and fails on CI.
    |
    | `mergeConfigFrom()` is a shallow merge, so this block must repeat the
    | package defaults it does not intend to change.
    |
    */

    'pages' => [
        'ensure_pages_exist' => false,

        'paths' => [
            resource_path('js/Pages'),
        ],

        'extensions' => [
            'js',
            'jsx',
            'svelte',
            'ts',
            'tsx',
            'vue',
        ],
    ],
];
