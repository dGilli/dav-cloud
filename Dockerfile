FROM composer:2.8.3 AS vendor
COPY composer.* .
RUN composer install

FROM php:8.0.30-apache
WORKDIR /var/www
COPY --from=vendor /app/vendor vendor
COPY src/ src/
COPY public/ public/
COPY bin/create_user /usr/local/bin/create_user
RUN mkdir data auth && \
    chmod a+rwx data public && \
    chmod +x /usr/local/bin/create_user && \
    sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/public|' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's|AllowOverride None|AllowOverride All|' /etc/apache2/apache2.conf && \
    a2enmod rewrite

