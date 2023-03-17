FROM php:7.4.32-cli-alpine3.16

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    @composer-1.10.22 \
    pdo_mysql

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_MEMORY_LIMIT -1

COPY ./conf.d /usr/local/etc/php/conf.d

RUN apk add bash

WORKDIR /app