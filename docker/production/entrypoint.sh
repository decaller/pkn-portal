#!/usr/bin/env sh
set -eu

cd /var/www/html

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache

# Create storage symlink if needed.
if [ ! -L public/storage ]; then
    php artisan storage:link || true
fi

# Cache framework and Filament metadata for production.
php artisan optimize --no-interaction || true
php artisan filament:optimize --no-interaction || true

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force --no-interaction
fi

exec "$@"
