FROM composer:2.8.3 AS vendor
COPY composer.* .
RUN composer install

FROM php:8.0.30-apache
WORKDIR /var/www/html
COPY --from=vendor /app/vendor vendor
COPY server.php .
RUN mkdir data public && \
    chmod a+rwx data public

