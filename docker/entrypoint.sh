#!/bin/sh
set -e

cd /var/www/html

mkdir -p \
  bootstrap/cache \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/testing \
  storage/framework/views \
  storage/logs

chmod -R ug+rwx bootstrap/cache storage || true

if [ ! -f .env ]; then
  if [ -f .env.docker.example ]; then
    cp .env.docker.example .env
  elif [ -f .env.example ]; then
    cp .env.example .env
  fi
fi

if [ ! -f vendor/autoload.php ]; then
  composer install --prefer-dist --no-interaction
fi

if [ -f artisan ] && [ -f .env ]; then
  if ! grep -Eq '^APP_KEY=base64:' .env; then
    php artisan key:generate --force || true
  fi

  php artisan config:clear || true
  php artisan route:clear || true
  php artisan view:clear || true
  php artisan migrate --force
  php artisan db:seed --force
fi

exec "$@"
