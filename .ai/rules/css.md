---
paths:
  - resources/css/app.css
---

# Css

## Button pointer cursor is restored globally, not per element
Tailwind v4's Preflight dropped v3's `button, [role="button"] { cursor: pointer }`, so the Tailwind 4 upgrade silently left every `<button>` on the Inertia frontend with the default arrow cursor. `app.css` restores it with the upgrade guide's compat rule in `@layer base`, using `:not(:disabled)`.

Do not add `cursor-pointer` to individual buttons — it is already global, and a base-layer rule still loses to any `cursor-*` utility, so per-element overrides like `cursor-not-allowed` keep working. Anchors (including Inertia `<Link>`) were never affected; they get pointer from the UA stylesheet.

`app.css` is loaded only on the Inertia frontend. The Filament panel ships its own compiled CSS and `welcome.blade.php` inlines frozen Tailwind v3 — neither is affected by changes here.
