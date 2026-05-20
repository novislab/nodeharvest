#!/bin/bash

# Exit on error
set -eux

# Run database migrations
php artisan migrate --force --seed

# Clear Laravel caches
php artisan optimize:clear

# Rebuild Laravel caches
php artisan optimize

# Build frontend assets
npm run build

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf