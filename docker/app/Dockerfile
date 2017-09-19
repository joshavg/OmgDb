FROM php:7-alpine

WORKDIR /app

RUN apk --no-cache add postgresql-dev
RUN docker-php-ext-install pdo_pgsql

RUN apk add --no-cache --virtual curl git
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

CMD php bin/console server:run 0.0.0.0:8000
