# The service's PHP-FPM, behind nginx (compose.yaml, docker/nginx.conf). It runs the service's
# folder as it is, mounted by compose.yaml, with its vendor/ (composer install). The official
# image has the mbstring and xml extensions the service needs.
FROM php:8.4-fpm-alpine
# Errors go to the log (docker compose logs), never to the Minitel
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
WORKDIR /var/www/app
