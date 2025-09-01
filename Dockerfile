FROM php:8.3-cli-alpine

RUN apk add --no-cache git unzip icu-dev libpq-dev libzip-dev \
  && docker-php-ext-install intl opcache pdo pdo_mysql pdo_pgsql zip

# Composer
RUN php -r "copy('https://getcomposer.org/installer','composer-setup.php');" \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
  && rm composer-setup.php

WORKDIR /app

# Plugins en root + prod avant install
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1 \
    APP_ENV=prod \
    APP_DEBUG=0

# 1) Manifests d'abord (cache Docker)
COPY composer.json composer.lock ./

# 2) Install SANS scripts (bin/console pas encore là)
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction --no-progress --prefer-dist

# 3) Copier le reste de l'app
COPY . .

# 4) Install AVEC scripts (cache:clear, assets:install…)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist \
 && php bin/console cache:clear --env=prod || true

# 5) Entrypoint: migrations + serveur PHP
COPY entrypoint.sh /app/entrypoint.sh
RUN chmod +x /app/entrypoint.sh

CMD ["/app/entrypoint.sh"]
