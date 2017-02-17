FROM php:7.1-apache

RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql

COPY php/php.ini /usr/local/etc/php/

RUN a2enmod rewrite


