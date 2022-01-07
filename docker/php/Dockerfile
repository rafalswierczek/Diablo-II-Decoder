FROM php:8.1.1-fpm

RUN apt-get update && apt-get install -y vim git zip unzip

#ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

#RUN chmod +x /usr/local/bin/install-php-extensions && install-php-extensions xdebug-3.1.2

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

CMD composer install ; php-fpm

EXPOSE 9000
