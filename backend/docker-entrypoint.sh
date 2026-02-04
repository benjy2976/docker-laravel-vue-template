#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
  if [ -f .env.example ]; then
    cp .env.example .env
  fi
fi

if [ -f .env ] && ! grep -q '^APP_KEY=' .env; then
  printf '\nAPP_KEY=\n' >> .env
fi

if [ -f .env ] && ! grep -q '^APP_KEY=base64:' .env; then
  if [ -f artisan ] && [ -f vendor/autoload.php ]; then
    php artisan key:generate
  fi
fi

exec "$@"
