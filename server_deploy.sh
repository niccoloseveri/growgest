#!/bin/bash
set -e

echo "Deployment started ..."
#c
# Enter maintenance mode or return true
# if already is in maintenance mode
(php82 artisan down --render="errors::update") || true

# Update codebase
git fetch origin deploy
git reset --hard origin/deploy

# Install composer dependencies
php82 /usr/local/bin/composer.phar install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Run database migrations
php82 artisan migrate --force

# Clear the old cache
php82 artisan clear-compiled

# Recreate cache
php82 artisan optimize

#Icon cache
php82 artisan icons:cache

#Filament cache
php82 artisan filament:optimize

# Exit maintenance mode
php82 artisan up

echo "Deployment finished!"
