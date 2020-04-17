FROM php:7.4-fpm-alpine AS php-symfony

#ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

#RUN chmod uga+x /usr/local/bin/install-php-extensions && sync
RUN install-php-extensions memcached pdo_mysql mysqli intl redis xdebug
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
RUN apk add yarn

WORKDIR /usr/src/app

COPY . /usr/src/app

VOLUME /usr/src/app

