#!/bin/sh
set -e

/usr/local/bin/wait-for-it database:3306 --

# Check if there are any tables in the database
if TABLES=$(mysql -h "$DB_HOST" -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "SHOW TABLES IN $MYSQL_DATABASE;"); then
    # Remove the first line (header)
    TABLES=$(echo "$TABLES" | sed -n '2,$p')

    if [ -z "$TABLES" ]; then
        echo "No tables found in the database. Initializing database setup..."

        # Remove all previous migrations
        echo "Removing all migration files..."
        rm -rf var/Migrations/*

        # Reset migration history in the database (only if the table exists)
        echo "Checking if migration_versions table exists..."
        TABLE_EXISTS=$(mysql -h "$DB_HOST" -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "SHOW TABLES LIKE 'migration_versions';" $MYSQL_DATABASE)

        if [ -z "$TABLE_EXISTS" ]; then
            echo "migration_versions table does not exist. Skipping reset."
        else
            echo "Resetting migration history in the database..."
            mysql -h "$DB_HOST" -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "DELETE FROM $MYSQL_DATABASE.migration_versions;"
        fi

        # Ensure migration files exist and are properly synchronized
        echo "Generating new migration files..."
        php bin/console doctrine:migrations:diff --allow-empty-diff --no-interaction

        echo "Running migrations to set up the database..."
        php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

        echo "Generating default articles..."
        php bin/console zm:generate-default-artikels
    else
        echo "Tables found in the database. Ensuring consistency..."
        echo "$TABLES"

        # Remove all previous migrations
        echo "Removing all migration files..."
        rm -rf var/Migrations/*

        # Reset migration history in the database (only if the table exists)
        echo "Checking if migration_versions table exists..."
        TABLE_EXISTS=$(mysql -h "$DB_HOST" -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "SHOW TABLES LIKE 'migration_versions';" $MYSQL_DATABASE)

        if [ -z "$TABLE_EXISTS" ]; then
            echo "migration_versions table does not exist. Skipping reset."
        else
            echo "Resetting migration history in the database..."
            mysql -h "$DB_HOST" -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "DELETE FROM $MYSQL_DATABASE.migration_versions;"
        fi

        # Generate new migration files
        php bin/console doctrine:migrations:diff --allow-empty-diff --no-interaction

        # Run migrations to update the database
        php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
    fi
else
    echo "Failed to connect to the database."
    echo "DB_HOST: $DB_HOST"
    echo "MYSQL_USER: $MYSQL_USER"
    echo "MYSQL_PASSWORD: $MYSQL_PASSWORD"
    echo "MYSQL_DATABASE: $MYSQL_DATABASE"
    exit 1
fi

# Clear and warmup the cache
echo "Clearing and warming up the cache..."
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug

# Start PHP-FPM
exec php-fpm
