FROM composer:2 AS dependencies

WORKDIR /app
COPY apps/backend/api/composer.json apps/backend/api/composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

FROM php:8.2-cli-alpine

RUN apk add --no-cache libpq-dev \
    && docker-php-ext-install pdo_pgsql

WORKDIR /app
COPY --from=dependencies /app/vendor ./vendor
COPY apps/backend/api/ .
COPY docker/scripts/backend-entrypoint.sh /usr/local/bin/backend-entrypoint
RUN chmod +x /usr/local/bin/backend-entrypoint \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && php artisan package:discover --ansi

EXPOSE 8000
ENTRYPOINT ["backend-entrypoint"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
