#!/bin/sh

# Check if certificates exist, if not, obtain them
if [ ! -f /etc/letsencrypt/live/iba.local.de/fullchain.pem ]; then
  echo "Certificates not found. Obtaining certificates..."
  certbot certonly --webroot -w /var/www/html -d iba.local.de --email rodion_kovalenko@yahoo.de --agree-tos --non-interactive
fi

# Start Nginx
nginx -g 'daemon off;'
