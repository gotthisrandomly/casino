#!/bin/bash

echo "Starting deployment..."

# Pull the latest changes from the git repository
git pull origin main

# Install/update composer dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader

# Clear the old cache
php artisan clear-compiled

# Recreate cache
php artisan optimize

# Compile npm assets
npm install
npm run build

# Run database migrations
php artisan migrate --force

# Clear and cache routes
php artisan route:cache

# Clear and cache config
php artisan config:cache

# Clear and cache views
php artisan view:cache

# Install/update cron jobs
php artisan schedule:work

echo "Deployment finished!"