#!/usr/bin/env bash

echo "Running Composer..."
composer install --no-dev --prefer-dist --optimize-autoloader

echo "Caching Laravel config..."
php artisan config:cache

echo "Caching Laravel routes..."
php artisan route:cache

echo "Caching Laravel views..."
php artisan view:cache

echo "Running migrations..."
php artisan migrate --force

echo "Build script completed."
