#!/bin/sh
set -e

/usr/local/bin/wait-for-it database:3306 --

# Check if there are any tables in the database
if TABLES=$(mysql -h "$DB_HOST" -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "SHOW TABLES IN $MYSQL_DATABASE;"); then
    # Remove the first line (header)
    TABLES=$(echo "$TABLES" | sed -n '2,$p')

    if [ -z "$TABLES" ]; then
        echo "No tables found in the database."
        echo "Database does not exist. Deleting migration files..."
        rm -rf var/Migrations/*
        php bin/console cache:clear --env=prod --no-debug
        php bin/console cache:warmup --env=prod --no-debug
        php bin/console doctrine:migrations:diff --allow-empty-diff --no-interaction
        php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
        php bin/console zm:generate-default-artikels
    else
        echo "Tables found in the database:"
        echo "$TABLES"
    fi
else
    echo "Failed to connect to the database."
    echo "DB_HOST: $DB_HOST"
    echo "MYSQL_USER: $MYSQL_USER"
    echo "MYSQL_PASSWORD: $MYSQL_PASSWORD"
    echo "MYSQL_DATABASE: $MYSQL_DATABASE"
fi

php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug
php bin/console doctrine:migrations:diff --allow-empty-diff --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Start PHP-FPM
exec php-fpm