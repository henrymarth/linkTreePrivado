FROM php:8.3-apache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY . .
RUN composer dump-autoload --no-dev --classmap-authoritative --no-interaction

ENV APP_ENV=production
ENV APP_URL=https://links.megamaxtech.com.br
ENV PUBLIC_BASE_URL=https://links.megamaxtech.com.br

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \
    CMD php -r 'exit(@file_get_contents("http://127.0.0.1/healthz") === false ? 1 : 0);'
