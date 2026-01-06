#!/bin/bash
set -e

echo "Starting Laravel setup..."

cd /var/www/html

mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

chmod -R 777 storage
chmod -R 777 bootstrap/cache

echo "Waiting for MySQL to be ready..."

until nc -z mysql 3306; do
    echo "Waiting for MySQL port 3306..."
    sleep 2
done

echo "MySQL port is open, waiting for service to be fully ready..."
sleep 10

echo "MySQL is ready. Setting up Laravel..."

if [ ! -d "vendor" ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction --optimize-autoloader --no-dev
fi

if [ -f "artisan" ]; then
    php artisan config:clear
    php artisan route:clear
    php artisan cache:clear
fi

echo "Laravel setup complete. Starting background services..."

/usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf &

sleep 5

echo "Starting PHP-FPM..."
exec php-fpm