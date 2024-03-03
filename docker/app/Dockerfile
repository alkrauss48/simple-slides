ARG PHP_VERSION=8.2
ARG INSTALL_BCMATH=true
ARG INSTALL_CALENDAR=false
ARG INSTALL_EXIF=true
ARG INSTALL_GD=true
ARG INSTALL_IMAGICK=true
ARG INSTALL_MOSQUITTO=false
ARG INSTALL_MYSQL=false
ARG INSTALL_OPCACHE=true
ARG INSTALL_PCNTL=true
ARG INSTALL_PGSQL=true
ARG INSTALL_REDIS=true
ARG INSTALL_SQLSRV=false
ARG INSTALL_XDEBUG=false
ARG INSTALL_ZIP=true
ARG INSTALL_INTL=true
ARG PHP_UPLOAD_MAX_FILESIZE 64m
ARG PHP_POST_MAX_SIZE 64m
ARG PHP_MEMORY_LIMIT 512m
ARG DEPS='nodejs npm chromium'

# Backend build
FROM ghcr.io/clevyr/php:$PHP_VERSION-base as php-builder
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
        --ignore-platform-reqs \
        --no-autoloader \
        --no-interaction \
        --no-progress \
        --no-suggest

COPY . .
RUN set -x \
    && export TELESCOPE_ENABLED=false \
    && composer dump-autoload \
        --classmap-authoritative \
        --no-interaction \
    && php artisan vendor:publish --tag=public


# Frontend build
FROM node:lts-alpine as node-builder
WORKDIR /app

RUN set -x \
    && apk add --no-cache \
        autoconf \
        automake \
        bash \
        g++ \
        libc6-compat \
        libjpeg-turbo \
        libjpeg-turbo-dev \
        libpng \
        libpng-dev \
        libtool \
        libwebp \
        libwebp-dev \
        make \
        nasm \
        python3 \
        cairo-dev \
        jpeg-dev \
        pango-dev \
        giflib-dev

ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD true
ENV CHROMIUM_PATH /usr/bin/chromium-browser

COPY artisan package.json package-lock.json ./
RUN npm ci

COPY --from=php-builder /app .
ARG NODE_ENV=production
RUN npm run build


# Local image
FROM ghcr.io/clevyr/php:$PHP_VERSION as local-image
WORKDIR /app

RUN npm --global install puppeteer@v17

# Install libraries for laravel-medialibrary
RUN set -x \
    && apk add --no-cache \
        ffmpeg \
        jpegoptim

COPY --chown=root docker/app/rootfs /

RUN crontab /etc/cron.d/scheduler

CMD ["s6-svscan", "/etc/s6/app"]


# Deployed image
FROM local-image

COPY --from=php-builder --chown=82:82 /app .
COPY --from=node-builder --chown=82:82 /app/public public/
