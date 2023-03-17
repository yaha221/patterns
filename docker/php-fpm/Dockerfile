FROM php:7.4.32-fpm-alpine3.16

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    pdo_mysql

COPY ./conf.d /usr/local/etc/php/conf.d

USER 1000

WORKDIR /app