FROM php:8.4-apache

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite \
    && printf '%s\n' '<Directory /var/www/html/yams>' '    AllowOverride FileInfo' '</Directory>' > /etc/apache2/conf-available/yams.conf \
    && a2enconf yams

COPY yams/ /var/www/html/yams/
COPY docker/config.php /var/www/html/yams/config/config.php

