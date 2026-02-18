FROM php:8.3-cli-alpine

RUN apk add --no-cache \
        git \
        unzip \
        curl-dev \
        ca-certificates \
    && docker-php-ext-install curl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
