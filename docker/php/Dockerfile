ARG DEPOT
FROM ${DEPOT}php:apache

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV DEBIAN_FRONTEND noninteractive

RUN apt upgrade -y --force-yes
RUN apt install -y --force-yes git zip unzip zlib1g-dev libpng-dev postgresql-client postgresql-server-dev-all build-essential

# Installation de xdebug
RUN pecl channel-update pecl.php.net
RUN pecl install xdebug-2.5.5
COPY conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN docker-php-ext-install gd pgsql pdo_pgsql

# Installation de composer
COPY --from=composer:2.2.18 /usr/bin/composer /usr/bin/composer

# Installation de symfony
# RUN composer require symfony/maker-bundle:"3.3.*" --dev

# Fix du bug Mutex
COPY apache2/apache2.conf /etc/apache2/apache2.conf

RUN a2enmod rewrite
RUN a2enmod headers