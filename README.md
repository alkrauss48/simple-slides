# Simple Slides

[Simple Slides](https://simpleslides.dev) is a web app built with:

* Laravel
* Vue + TypeScript
* PostgreSQL

that allows for simple and content-friendly rendering of presentation
slides via Markdown.

Simple Slides follows the [Takahashi Method]() of presenting slides, which
focuses on:

* Prioritizing text-content
* Low amount of content per slide
* Many slides, and changing through slides quickly

## To Install
This project uses Laravel Sail, which is basically a built-in Docker wrapper for
Laravel applications.
[View their documentation](https://laravel.com/docs/10.x/sail#installation)
for more details:
```sh
composer require laravel/sail --dev
php artisan sail:install
```

Then, set an alias for the `sail` command, to make it much shorter to use:
```sh
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

## To Run
```sh
# In one terminal:
sail up

# Then, in another
sail npm run dev

```

## Autoformatting
```
# Laravel

sail pint
```

## Linting
```
# PHP
sail bin phpstan analyse

# TS
sail npx vue-tsc
```

## Running the Tests
```sh
# Back-end (Laravel)
sail artisan test

# Front-end (Vue)
sail npx vitest
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
