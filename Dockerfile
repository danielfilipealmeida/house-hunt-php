FROM php:7.2-apache
RUN echo "Setting up Web Container" \
    && apt-get update && apt-get install -y \
        libzip-dev \
        memcached \
        libmemcached-dev \
        libmemcached-tools \
        nano \
        net-tools \
        iputils-ping \
        netcat \
        jnettop \
        telnet

RUN pecl install memcached-3.0.4 \
    && pecl install xdebug-2.7.1
RUN docker-php-ext-enable memcached 
#RUN docker-php-ext-enable xdebug 


RUN docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-install pdo pdo_mysql

RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load