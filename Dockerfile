FROM php:8.3-cli-alpine

RUN apk add --no-cache git unzip icu-dev libpq-dev libzip-dev \
  && docker-php-ext-install intl opcache pdo pdo_mysql pdo_pgsql zip

# Installer Composer
RUN php -r "copy('https://getcomposer.org/installer','composer-setup.php');" \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
  && rm composer-setup.php

WORKDIR /app

# ⚡ Etape 1 : copier uniquement composer.* pour profiter du cache Docker
COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-interaction

# ⚡ Etape 2 : copier le reste du projet
COPY . .

# Re-clear du cache Symfony
RUN php bin/console cache:clear --env=prod || true

ENV APP_ENV=prod \
    APP_DEBUG=0

CMD php -S 0.0.0.0:${PORT:-8080} -t public
