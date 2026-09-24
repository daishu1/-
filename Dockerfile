FROM php:8.3-apache
RUN docker-php-ext-install mysqli
COPY . /var/www/html/
COPY deploy/init-admin.php /var/www/html/deploy/init-admin.php
RUN chown -R www-data:www-data /var/www/html
