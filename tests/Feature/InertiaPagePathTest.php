<?php

/**
 * macOS resolves `resources/js/pages` to `resources/js/Pages`, so a page path
 * configured with the wrong case only fails on a case-sensitive CI filesystem.
 * Comparing against the directory listing catches it on any machine.
 */
it('configures inertia page paths that exist with the same case on disk', function () {
    $paths = config('inertia.pages.paths');

    expect($paths)->not->toBeEmpty();

    foreach ($paths as $path) {
        expect(scandir(dirname($path)))->toContain(basename($path));
    }
});

it('resolves every page component rendered by the application', function () {
    $components = ['AdhocSlides', 'Presentation', 'Privacy', 'Profile', 'Settings'];

    foreach ($components as $component) {
        expect(app('inertia.view-finder')->find($component))->toBeString();
    }
});
