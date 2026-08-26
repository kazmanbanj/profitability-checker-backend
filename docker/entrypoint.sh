#!/bin/sh

set -e

echo "Running database migrations..."
php artisan migrate --force

echo "Starting application..."
exec "$@"