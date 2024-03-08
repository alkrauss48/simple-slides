# Simple Slides

[Simple Slides](https://simpleslides.dev) is a response and text-first
presentation tool that keeps your audience engaged, and is built with:

* Laravel
* Vue + TypeScript
* PostgreSQL

Presentations built with Simple Slides typically focus on:

* Prioritizing text-content
* Low amount of content per slide
* Many slides, and changing through slides quickly

## To Install
```sh
composer install
```

This project uses Laravel Sail, which is basically a built-in Docker wrapper for
Laravel applications.
[View their documentation](https://laravel.com/docs/10.x/sail#installation)
for more details.

Ideally, you should set an alias for the `sail` command, to make it much shorter to use:
```sh
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

Note: If you have run a Laravel Sail project before, you may have already created this
alias.

## To Run
```sh
# In one terminal:
sail up

# Then, in another
sail npm run dev

```

## Autoformatting
Via [Laravel pint](https://laravel.com/docs/10.x/pint)
```
# Laravel

sail pint
```

## Linting
```
# Back-end (Laravel)
sail bin phpstan analyse

# Front-end (Vue)
sail npx vue-tsc
```

## Running the Tests
```sh
# Back-end (Laravel)
sail artisan test

# Front-end (Vue)
sail npx vitest
```

## To Generate Thumbnails for Presentations
This uses puppeteer and Browsershot, which requires some extra config on top of
Laravel Sail. To configure this, you need to attach to the container and run the
`./docker/sail-extra.sh` command:

```
docker compose exec laravel.test sh
> ./docker/sail-extra.sh
```

## Recommended IDE Setup

- [VS Code](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (and disable Vetur) + [TypeScript Vue Plugin (Volar)](https://marketplace.visualstudio.com/items?itemName=Vue.vscode-typescript-vue-plugin).

## Type Support For `.vue` Imports in TS

TypeScript cannot handle type information for `.vue` imports by default, so we replace the `tsc` CLI with `vue-tsc` for type checking. In editors, we need [TypeScript Vue Plugin (Volar)](https://marketplace.visualstudio.com/items?itemName=Vue.vscode-typescript-vue-plugin) to make the TypeScript language service aware of `.vue` types.

If the standalone TypeScript plugin doesn't feel fast enough to you, Volar has also implemented a [Take Over Mode](https://github.com/johnsoncodehk/volar/discussions/471#discussioncomment-1361669) that is more performant. You can enable it by the following steps:

1. Disable the built-in TypeScript Extension
   1. Run `Extensions: Show Built-in Extensions` from VSCode's command palette
   2. Find `TypeScript and JavaScript Language Features`, right click and select `Disable (Workspace)`
2. Reload the VSCode window by running `Developer: Reload Window` from the command palette.
