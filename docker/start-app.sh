#!/bin/sh
# Copy nginx config into place
cp /etc/app/nginx.conf /etc/nginx/http.d/default.conf
 
# Run migrations on startup (safe to run repeatedly)
php artisan migrate --force
 
# Start PHP-FPM in background, then Nginx in foreground
php-fpm -D
nginx -g 'daemon off;'
