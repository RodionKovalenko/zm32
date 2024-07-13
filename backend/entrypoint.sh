#!/bin/sh
set -e

/usr/local/bin/wait-for-it database:3306 --
php bin/console cache:clear --no-warmup
php bin/console doctrine:migrations:diff --allow-empty-diff --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction
#php bin/console zm:generate-default-artikels

# Start PHP-FPM
exec php-fpm