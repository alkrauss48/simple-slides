# Simple Slides

[Simple Slides](https://simpleslides.dev) is a responsive and text-first
presentation tool that keeps your audience engaged, and is built with:

-   Laravel 13 (PHP 8.5)
-   Filament 5 — powers the admin panel and all authentication
-   Inertia 3 + Vue 3 + TypeScript
-   Tailwind CSS 4
-   PostgreSQL 15

Presentations built with Simple Slides typically focus on:

-   Prioritizing text-content
-   Low amount of content per slide
-   Many slides, and changing through slides quickly

## Requirements

-   Docker (everything else runs inside Laravel Sail's containers)
-   PHP 8.5 on the host, only for the very first `composer install`. If your
    host PHP is older, run the install through Docker instead:

    ```sh
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php85-composer:latest \
        composer install --ignore-platform-reqs
    ```

-   Node 24 is what CI uses. It runs inside the container, so you do not need it
    on the host.

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
[View their documentation](https://laravel.com/docs/13.x/sail#installation)
for more details.

## Ports

This project does **not** use Sail's default ports. `.env.example` pins a unique
port for every container so that several Laravel applications can run side by
side without colliding:

| Service            | Env var                         | Host port | URL                      |
| ------------------ | ------------------------------- | --------- | ------------------------ |
| Application        | `APP_PORT`                      | 7070      | http://localhost:7070    |
| Admin panel        | —                               | 7070      | http://localhost:7070/admin |
| Vite dev server    | `VITE_PORT`                     | 51730     | —                        |
| Mailpit dashboard  | `FORWARD_MAILPIT_DASHBOARD_PORT`| 18025     | http://localhost:18025   |
| Mailpit SMTP       | `FORWARD_MAILPIT_PORT`          | 10250     | —                        |
| PostgreSQL         | `FORWARD_DB_PORT`               | 54320     | —                        |
| Redis              | `FORWARD_REDIS_PORT`            | 63790     | —                        |

Two things to watch for:

-   `APP_PORT` and `APP_URL` must be changed together. If you move the app off
    7070 and leave `APP_URL` pointing at the old port, generated URLs, signed
    invitation links, and asset URLs all break.
-   Port changes only take effect on container **create**, not restart. After
    editing any of these, run `sail down && sail up -d`.

Use the PostgreSQL and Redis ports above to connect host-side tools such as
TablePlus or a Redis client.

## To Run

```sh
# In one terminal, to run the app:
sail up -d

# Then run the migrations and seeders (this command will end)
sail artisan migrate:fresh --seed

# Then, build, watch, and hot reload for front-end changes
sail npm run dev
```

The app is then at http://localhost:7070, and any mail the app sends is caught
by Mailpit at http://localhost:18025.

To log in, go to http://localhost:7070/admin — Filament owns authentication, so
that is the only login entry point. Use any of the following credentials (found
in `database/seeders/DatabaseSeeder.php`):

-   Email: `admin@example.com` for an admin user, or `test@example.com` for a
    non-admin user.
-   Password: Both accounts use `password`.

## Building for Production

```sh
sail npm run build
```

Note that `build` runs `vue-tsc` before Vite, so a **type error fails the build**,
not just the lint step.

## Autoformatting

Via [Laravel pint](https://laravel.com/docs/13.x/pint), which is a
code-formatter following Laravel's best practices. **Note:** This is not a
linter, and it is not enforced by CI — run it yourself before pushing.

```sh
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

```sh
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

# Front-end (Vue), in watch mode
sail npx vitest

# Front-end, single run (what CI does)
sail npx vitest --run
```

## Continuous Integration

`.github/workflows/ci.yml` runs on every push, against PHP 8.5 and Node 24:

| Job            | Command                     |
| -------------- | --------------------------- |
| Front-end Lint | `vue-tsc`                   |
| Back-end Lint  | `phpstan analyse`           |
| Front-end Test | `vitest --run`              |
| Back-end Test  | `pest`                      |
| Build          | Builds and pushes the production image to Docker Hub |

Pint is deliberately **not** part of CI, so formatting will not fail a build —
run `sail pint` locally.

## Production

Production does not use Sail. It runs the image built by `docker/app/Dockerfile`,
which serves nginx + php-fpm under s6, plus a queue worker and the cron-driven
scheduler.

-   `docker-compose.prod.yml` — app, PostgreSQL, and Redis
-   `.env.prod.example` — copy to `.env.prod` and fill in

Production differs from local in a few meaningful ways:

-   Cache and sessions use Redis rather than the file driver.
-   Media and Filament uploads live on Digital Ocean Spaces (`FILESYSTEM_DISK=do`,
    `MEDIA_DISK=do`), so the `DO_*` credentials are required.
-   Mail goes out through Sendgrid instead of Mailpit.
-   The image runs a `queue:work` worker, but `.env.prod.example` ships
    `QUEUE_CONNECTION=sync`, so nothing reaches it until you point the queue at
    Redis.

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

## Project Conventions

Settled decisions, non-obvious traps, and standing constraints live in
`.ai/rules/`. Start at `.ai/rules/index.md`, which maps file globs to the rule
file that covers them. `CLAUDE.md` and `AGENTS.md` carry the framework and
package guidelines for AI agents working in this repo.

One constraint worth knowing before you touch `package.json`:

> **Do not upgrade `typescript` past 6.x.** TS 7 is the native Go port, and its
> package exports drop the JS compiler API. That breaks `vue-tsc` outright and
> also fails `vite build` inside `@vue/compiler-sfc`. See
> `.ai/rules/general.md` for the details.

## Recommended IDE Setup

-   [VS Code](https://code.visualstudio.com/) +
    [Vue - Official](https://marketplace.visualstudio.com/items?itemName=Vue.volar)
    (and disable Vetur).

The separate "TypeScript Vue Plugin (Volar)" extension and Volar's Take Over
Mode are both obsolete — everything they did is built into Vue - Official as of
v2, so installing the Vue extension alone is enough.

## Type Support For `.vue` Imports in TS

TypeScript cannot handle type information for `.vue` imports by default, so we
replace the `tsc` CLI with `vue-tsc` for type checking, both locally and in CI.
In the editor, the Vue - Official extension makes the TypeScript language
service aware of `.vue` types.
