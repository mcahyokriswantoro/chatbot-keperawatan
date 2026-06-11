#!/bin/bash
# Jalankan di Terminal cPanel Qwords (setelah clone repo + document root → public)
set -e

APP_DIR="${1:-$HOME/chatbot-keperawatan.damgocompany.com}"

cd "$APP_DIR"

echo "==> PHP version"
php -v | head -1

echo "==> Composer install"
if command -v composer >/dev/null 2>&1; then
  composer install --no-dev --optimize-autoloader --no-interaction
else
  /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction
fi

if [ ! -f .env ]; then
  echo "ERROR: File .env belum ada. Salin dari deploy/env-production.example dulu."
  exit 1
fi

echo "==> Laravel setup"
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan ayosehat:sync

chmod -R 775 storage bootstrap/cache

echo "==> Selesai. Buka https://chatbot-keperawatan.damgocompany.com"
