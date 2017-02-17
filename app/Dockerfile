FROM php:7.1-apache

RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql
RUN pecl install xdebug-2.5.0 \
      && docker-php-ext-enable xdebug

COPY php/php.ini /usr/local/etc/php/

RUN a2enmod rewrite


