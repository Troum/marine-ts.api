#!/bin/sh
set -e
cd /var/www/html

if [ -z "$APP_KEY" ]; then
  export APP_KEY=$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")
fi

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

php artisan package:discover --ansi --no-interaction

php artisan migrate --force

# Роли и права (idempotent). Полный DatabaseSeeder не запускаем здесь — он чистит пользователей/контент (MarineDataSeeder).
php artisan db:seed --class=RolePermissionSeeder --force

exec php artisan serve --host=0.0.0.0 --port=8000
