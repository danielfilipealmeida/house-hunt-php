FROM php:7.2-apache
RUN echo "Setting up Web Container" \
    && apt-get update && apt-get install -y \
        libzip-dev \
        memcached \
        libmemcached-dev \
        libmemcached-tools \
    && docker-php-ext-install -j$(nproc) zip \
    && pecl install memcached-3.0.4 \
    && pecl install xdebug \
    && docker-php-ext-enable memcached \
    && docker-php-ext-install pdo pdo_mysql 