# The service's PHP-FPM, behind nginx (compose.yaml, docker/nginx.conf). The official image has
# the mbstring and xml extensions the service needs.
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock* ./
# The packages the service runs with, not its tools (tests, Mago)
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --no-progress
COPY src src
RUN composer dump-autoload --no-dev --classmap-authoritative

FROM php:8.4-fpm-alpine
# Errors go to the log (docker compose logs), never to the Minitel
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
WORKDIR /var/www/app
COPY --from=vendor /app/vendor vendor
COPY config config
COPY public public
COPY src src
