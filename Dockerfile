FROM php:8.2-apache

# PHP razširitev za povezavo z MySQL/MariaDB (PDO)
RUN docker-php-ext-install pdo_mysql

# XAMPP ima output_buffering vklopljen, uradna PHP slika pa ne.
# Brez tega header('Location: ...') ne deluje, ker header.php že izpiše HTML.
RUN echo "output_buffering = 4096" > /usr/local/etc/php/conf.d/app.ini

# Aplikacija uporablja poti /thesismatch/..., zato jo damo v to podmapo
COPY code/thesismatch/ /var/www/html/thesismatch/

# Obisk http://localhost:8080/ preusmeri na aplikacijo
RUN echo '<?php header("Location: /thesismatch/index.php");' > /var/www/html/index.php

EXPOSE 80