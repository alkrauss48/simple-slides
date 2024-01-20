# PHP Builder
FROM sail-8.2/app

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/html

RUN composer install \
        --ignore-platform-reqs \
        --no-autoloader \
        --no-interaction \
        --no-progress

COPY package.json package-lock.json /var/www/html
RUN npm ci

# Copy existing application directory contents
COPY . /var/www/html

ARG NODE_ENV=production
RUN npm run build

RUN set -x \
    && composer dump-autoload \
        --classmap-authoritative \
        --no-interaction

RUN chmod -R a+w storage
