FROM php:8.3-cli-alpine

RUN apk add --no-cache git unzip icu-dev libpq-dev libzip-dev \
  && docker-php-ext-install intl opcache pdo pdo_mysql pdo_pgsql zip

# Composer
RUN php -r "copy('https://getcomposer.org/installer','composer-setup.php');" \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
  && rm composer-setup.php

WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1

# 1) Copier uniquement les manifests
COPY composer.json composer.lock ./

# 2) Installer SANS scripts (bin/console n'est pas encore là)
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction --no-progress --prefer-dist

# 3) Copier le reste du projet (inclut bin/console)
COPY . .

# 4) Rejouer un install (rapide grâce au cache vendor) pour EXÉCUTER les auto-scripts
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# 5) (sécurité) cache clear au cas où
RUN php bin/console cache:clear --env=prod || true

ENV APP_ENV=prod \
    APP_DEBUG=0

CMD php -S 0.0.0.0:${PORT:-8080} -t public
