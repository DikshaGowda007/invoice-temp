#!/bin/sh
set -e

echo "Starting Laravel queue worker..."

php artisan config:clear
php artisan config:cache

exec php artisan queue:work redis \
    --tries=3 \
    --backoff=10 \
    --sleep=3
