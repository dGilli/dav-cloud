FROM composer:2.8.3 AS vendor
COPY composer.* .
RUN composer install

FROM php:8.0.30-apache
WORKDIR /var/www
COPY --from=vendor /app/vendor vendor
COPY src/ src/
COPY web/ html/
COPY bin/create_user /usr/local/bin/create_user
RUN mkdir data data/public && \
    chown -R www-data:www-data data && \
    chmod a+rwx data/public && \
    a2enmod rewrite

