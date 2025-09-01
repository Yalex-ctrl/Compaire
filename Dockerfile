FROM php:8.3-cli-alpine

RUN apk add --no-cache git unzip icu-dev libpq-dev libzip-dev \
  && docker-php-ext-install intl opcache pdo pdo_mysql pdo_pgsql zip

# Composer
RUN php -r "copy('https://getcomposer.org/installer','composer-setup.php');" \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
  && rm composer-setup.php

WORKDIR /app

# ✅ Avant les installs : activer plugins en root + forcer prod
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1 \
    APP_ENV=prod \
    APP_DEBUG=0

# 1) Copier uniquement les manifests pour profiter du cache
COPY composer.json composer.lock ./

# 2) Installer SANS scripts (bin/console pas encore présent)
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction --no-progress --prefer-dist

# 3) Copier le reste du projet (inclut bin/console)
COPY . .

# 4) Rejouer un install (avec scripts) → cache:clear/ assets:install en prod
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# 5) Sécurité : clear cache prod (ne bloque pas si rien à faire)
RUN php bin/console cache:clear --env=prod || true

# Render expose $PORT
CMD php -S 0.0.0.0:${PORT:-8080} -t public
