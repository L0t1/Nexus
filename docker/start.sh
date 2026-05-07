#!/bin/sh

# Render binds to the $PORT environment variable. Default to 80 if not set.
PORT=${PORT:-80}

# Update Apache configuration to listen on the correct port
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Run Laravel optimizations and link storage
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

# Run migrations automatically (safe in production with --force)
php artisan migrate --force

# Start Apache in foreground
apache2-foreground
