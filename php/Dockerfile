FROM php:7.0-fpm

RUN apt-get update && \
    apt-get install vim libldap2-dev -y
RUN docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/
RUN docker-php-ext-install pdo_mysql ldap mbstring
RUN docker-php-ext-enable ldap
