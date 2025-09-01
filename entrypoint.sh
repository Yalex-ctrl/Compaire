#!/bin/sh
set -e

echo "[entrypoint] APP_ENV=${APP_ENV:-prod}"
echo "[entrypoint] DATABASE_URL=${DATABASE_URL}"

echo "[entrypoint] Lancement des migrations Doctrine..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod || true

echo "[entrypoint] Démarrage du serveur PHP..."
exec php -S 0.0.0.0:${PORT:-8080} -t public