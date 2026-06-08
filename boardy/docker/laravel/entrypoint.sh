#!/bin/sh
set -e
cd /var/www/html

mkdir -p storage/framework/cache storage/framework/sessions \
         storage/framework/views storage/logs storage/app/public \
         bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

if [ ! -s storage/oauth-private.key ]; then
    php artisan passport:keys --force || true
fi
chmod 644 storage/oauth-public.key 2>/dev/null || true
chmod 600 storage/oauth-private.key 2>/dev/null || true
chown www-data:www-data storage/oauth-*.key 2>/dev/null || true

exec "$@"
