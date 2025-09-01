FROM php:8.3-cli-alpine

# Paquets + extensions PHP (ajuste si besoin)
RUN apk add --no-cache git unzip icu-dev libpq-dev libzip-dev \
  && docker-php-ext-install intl opcache pdo pdo_mysql pdo_pgsql zip

# Composer
RUN php -r "copy('https://getcomposer.org/installer','composer-setup.php');" \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
  && rm composer-setup.php

WORKDIR /app

# ⚠️ Autoriser les plugins Composer en root (sinon Flex est désactivé → erreur 127)
ENV COMPOSER_ALLOW_SUPERUSER=1

# 1) Manifests d'abord pour profiter du cache
COPY composer.json composer.lock ./

# 2) Installer les dépendances prod
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# 3) Copier le reste du projet
COPY . .

# 4) Préparer le cache (ne plante pas si .env incomplet)
RUN php bin/console cache:clear --env=prod || true

# Vars (Render les écrasera si définies côté dashboard)
ENV APP_ENV=prod \
    APP_DEBUG=0

# Render fournit $PORT → on sert via PHP built-in (OK pour ton petit trafic)
CMD php -S 0.0.0.0:${PORT:-8080} -t public
