---
paths:
  - resources/views/welcome.blade.php
---

# Views

## Never run Tailwind codemods over welcome.blade.php or published Filament CSS
`welcome.blade.php` is self-contained: it has no `@vite` directive and ships its own frozen compiled Tailwind v3 CSS in an inline `<style>` block. Class renames in its markup will NOT be matched by that stylesheet and silently break the page.

`@tailwindcss/upgrade` rewrites both this file and `public/css/filament/filament/app.css` (published vendor CSS, ~62k lines). Revert both after running any Tailwind codemod:

    git checkout -- resources/views/welcome.blade.php public/css/filament/filament/app.css
