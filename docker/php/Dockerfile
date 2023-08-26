ARG PHP_VERSION=8.2

FROM php:${PHP_VERSION}-apache

#default
RUN apt-get -y update && apt-get upgrade -y && apt-get install -y \
      git \
      unzip \
      libzip-dev \
      libpq-dev \
      git \
      unzip \
      libzip-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install \
      pgsql \
      pdo \
      pdo_pgsql \
      zip

#xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

#intl
RUN apt-get -y update \
    && apt-get install -y libicu-dev\
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

#composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#clean trash
RUN rm -rf /tmp/* \
    && rm -rf /var/list/apt/* \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean
