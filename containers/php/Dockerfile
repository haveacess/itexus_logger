FROM php:8.1-fpm-alpine3.15

ENV COMPOSER_ALLOW_SUPERUSER=1

#install composer locally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer