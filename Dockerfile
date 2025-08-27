# Image PHP légère
FROM php:8.3-cli-alpine

# Extensions utiles (PDO MySQL/Postgres, intl, zip, opcache...)
RUN apk add --no-cache git unzip icu-dev libpq-dev libzip-dev \
  && docker-php-ext-install intl opcache pdo pdo_mysql pdo_pgsql zip

# Composer
RUN php -r "copy('https://getcomposer.org/installer','composer-setup.php');" \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
  && rm composer-setup.php

WORKDIR /app
COPY . /app

# Dépendances en mode prod
RUN composer install --no-dev --optimize-autoloader --no-interaction || true \
  && php bin/console cache:clear --env=prod || true

# Variables d'env (Render les écrasera)
ENV APP_ENV=prod \
    APP_DEBUG=0

# Render fournit $PORT → on l’utilise
CMD php -S 0.0.0.0:${PORT:-8080} -t public
