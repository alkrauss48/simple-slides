---
paths:
  - package.json
---

# General

## TypeScript is capped at 6.x — TS 7 breaks the Vue build
Do not upgrade `typescript` to 7.x. TS 7 is the native Go port and its package `exports` drop the JS compiler API (only `./lib/version.cjs` and `./unstable/*` remain).

Two things break, not just type-checking:
- `vue-tsc` dies immediately — it patches `typescript/lib/tsc`, which no longer ships.
- `vite build` fails in `@vue/compiler-sfc`, which falls back to `ts.sys` (`const fs = ctx.options.fs || ts?.sys`) to resolve imported types in `defineProps<T>()`. AdhocSlides.vue and Presentation.vue hit this via `QueryParams`/`Presentation`.

TS 6.0.3 is the last JS-based release and works with zero workarounds. Revisit 7 only once `@vue/compiler-sfc` and `vue-tsc` support it.
