# PHP Builder
FROM php:8.2-fpm as php-builder

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    zlib1g-dev \
    libpq-dev \
    libzip-dev

# compile native PHP packages
RUN docker-php-ext-install \
    gd \
    pcntl \
    bcmath \
    mysqli \
    pdo pdo_pgsql pgsql

# configure packages
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# install additional packages from PECL
RUN pecl install zip && docker-php-ext-enable zip \
    && pecl install igbinary && docker-php-ext-enable igbinary

RUN pecl install redis && docker-php-ext-enable redis

# RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
#   && docker-php-ext-install pdo pdo_pgsql pgsql zip bcmath gd

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
# RUN docker-php-ext-install pdo_mysql zip exif pcntl
# RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
# RUN docker-php-ext-install gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/html

RUN composer install \
        --ignore-platform-reqs \
        --no-autoloader \
        --no-interaction \
        --no-progress

# Copy existing application directory contents
COPY . /var/www/html

RUN set -x \
    && composer dump-autoload \
        --classmap-authoritative \
        --no-interaction

# Copy existing application directory permissions
COPY ./docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

RUN chmod -R a+w storage

# Change current user to www
USER www

### END PHP BUILDER

# Frontend build
FROM node:18-alpine as node-builder
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY --from=php-builder /var/www/html .
ARG NODE_ENV=production
RUN npm run build

# Final PHP Image
FROM php-builder as php-image

COPY --from=node-builder --chown=82:82 /app/public /var/www/html/public/

# Start php-fpm server
CMD ["php-fpm"]

# Final Nginx Image
FROM nginx:stable-alpine as nginx-image
WORKDIR /var/www/html

RUN mkdir -p /run/nginx
COPY --from=php-builder --chown=82:82 /var/www/html/public public
COPY --from=node-builder --chown=82:82 /app/public public
COPY ./docker/nginx/nginx.conf /etc/nginx
