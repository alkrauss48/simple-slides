---
paths:
  - config/inertia.php
---

# Config

## Inertia page paths must stay capitalized as js/Pages
Inertia v3 defaults `inertia.pages.paths` to `resource_path('js/pages')`, but this app capitalizes its frontend directories (`Pages`, `Components`, `Layouts`) and `resources/js/app.ts` globs `./Pages/**/*.vue`. `config/inertia.php` overrides the path to `resource_path('js/Pages')`.

Without it, `assertInertia` passes on macOS (case-insensitive APFS) and fails on CI with "Inertia page component file [X] does not exist." Sail does not reproduce it either, since the bind mount inherits the host's case-insensitivity.

`mergeConfigFrom()` is a shallow merge, so the `pages` block in `config/inertia.php` must repeat `extensions` and `ensure_pages_exist` even though it only changes `paths`. `tests/Feature/InertiaPagePathTest.php` guards the casing against the directory listing.
