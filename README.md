# Simple Slides

[Simple Slides](https://simpleslides.dev) is a responsive and text-first
presentation tool that keeps your audience engaged, and is built with:

-   Laravel
-   Vue + TypeScript
-   PostgreSQL

Presentations built with Simple Slides typically focus on:

-   Prioritizing text-content
-   Low amount of content per slide
-   Many slides, and changing through slides quickly

## To Install

Ideally, you should set an alias for the `sail` command, to make it much shorter to use:

```sh
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

Note: If you have run a Laravel Sail project before, you may have already created this
alias.

-   Locate the .env.example file in your project directory.
-   Create a new file named .env in the same directory.
-   Copy the entire content of the .env.example file and paste it into the newly created .env file.

Note: This file will hold your environment variables.

Run below commands in your terminal

```sh
composer install
sail npm install
sail artisan key:generate
```

This project uses Laravel Sail, which is basically a built-in Docker wrapper for
Laravel applications.
[View their documentation](https://laravel.com/docs/10.x/sail#installation)
for more details.

## To Run

```sh
# In one terminal, to run the app:
sail up

# Then, in another to run the migrations and seeders (this command will end)
sail artisan migrate:fresh --seed

# Then, build, watch, and hot reload for front-end changes
sail npm run dev

```

To log in, use any of the following credentials (found in
database/Seeders/DatabaseSeeder.php):

-   Email: `admin@example.com` for an admin user, or `test@example.com` for a
    non-admin user.
-   Password: Both accounts use `password`.

## Autoformatting

Via [Laravel pint](https://laravel.com/docs/10.x/pint), which is a
code-formatter following Laravel's best practices. **Note:** This is not a
linter.

```
# Laravel

sail pint
```

## Linting

Linting on the back-end (e.g. Laravel) is done via
[larastan](https://github.com/larastan/larastan), which is a
[PHPStan](https://phpstan.org/) wrapper for Laravel.

Linting on the front-end (e.g. Vue w/ Typescript) is done with `vue-tsc`, which
is a first-party wrapper for Vue around `tsc`. See more here:
https://vuejs.org/guide/typescript/overview

```
# Back-end (Laravel)
sail bin phpstan analyse

# Front-end (Vue)
sail npx vue-tsc
```

## Running the Tests

Tests on the back-end (e.g. Laravel) are written using
[Pest](https://pestphp.com/), and are a
mostly integration and end-to-end tests, with some unit tests sprinkled in.
Currently there are no browser tests.

Tests on the front-end (e.g. Vue w/ Typescript) are written using
[vitest](https://vitest.dev/), along with some other test utilities like [Vue
Test Utils](https://test-utils.vuejs.org/) and [Mock Service
Worker](https://mswjs.io/).

```sh
# Back-end (Laravel)
sail artisan test

# Front-end (Vue)
sail npx vitest
```

## To Generate Thumbnails for Presentations

Thumbnails are rendered by [Browsershot](https://spatie.be/docs/browsershot),
which drives a headless Chrome through puppeteer. The Sail image already has
every library Chrome needs, but the Chrome binary itself is downloaded
separately and is not part of the image:

```sh
sail npx puppeteer browsers install chrome
```

Run this once after `sail npm install`. You also need to re-run it:

-   After the container is rebuilt or recreated. The download lives in
    `/home/sail/.cache/puppeteer` inside the container, which is not on the
    bind mount, so it does not survive.
-   After `puppeteer` is upgraded, because each puppeteer release pins a
    specific Chrome build.

In either case, the "Generate Thumbnail" action fails with
`Error: Could not find Chrome (ver. <version>)`. Re-running the install command
above fixes it.

To check what is currently installed:

```sh
sail ls /home/sail/.cache/puppeteer/chrome
```

The directory name (e.g. `linux-153.0.8010.36`) must match the version in
`node_modules/puppeteer-core/lib/puppeteer/revisions.js`.

## Recommended IDE Setup

-   [VS Code](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (and disable Vetur) + [TypeScript Vue Plugin (Volar)](https://marketplace.visualstudio.com/items?itemName=Vue.vscode-typescript-vue-plugin).

## Type Support For `.vue` Imports in TS

TypeScript cannot handle type information for `.vue` imports by default, so we replace the `tsc` CLI with `vue-tsc` for type checking. In editors, we need [TypeScript Vue Plugin (Volar)](https://marketplace.visualstudio.com/items?itemName=Vue.vscode-typescript-vue-plugin) to make the TypeScript language service aware of `.vue` types.

If the standalone TypeScript plugin doesn't feel fast enough to you, Volar has also implemented a [Take Over Mode](https://github.com/johnsoncodehk/volar/discussions/471#discussioncomment-1361669) that is more performant. You can enable it by the following steps:

1. Disable the built-in TypeScript Extension
    1. Run `Extensions: Show Built-in Extensions` from VSCode's command palette
    2. Find `TypeScript and JavaScript Language Features`, right click and select `Disable (Workspace)`
2. Reload the VSCode window by running `Developer: Reload Window` from the command palette.
