FROM php:8.2-apache

# PHP razširitvi: pdo_mysql za MariaDB, redis za shranjevanje sej v Redis
RUN docker-php-ext-install pdo_mysql \
 && pecl install redis \
 && docker-php-ext-enable redis

# Naše PHP nastavitve (varnost + seje v Redisu)
COPY php/thesis.ini /usr/local/etc/php/conf.d/thesis.ini

# Aplikacija uporablja poti /thesismatch/..., zato jo damo v to podmapo
COPY code/thesismatch/ /var/www/html/thesismatch/

# Obisk http://localhost:8080/ preusmeri na aplikacijo
RUN echo '<?php header("Location: /thesismatch/index.php");' > /var/www/html/index.php

EXPOSE 80