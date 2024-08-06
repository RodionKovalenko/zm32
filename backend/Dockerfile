FROM php:8.2-fpm

USER root

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies and tools
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    zlib1g-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    netcat-openbsd \
    default-mysql-client \
    dos2unix \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql intl zip opcache gd bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/symfony

# Copy Composer files
COPY composer.json composer.lock ./

# Set permissions for Composer cache and vendor directory
RUN mkdir -p /var/www/.composer/cache && \
    chown -R www-data:www-data /var/www/.composer && \
    mkdir -p /var/www/symfony/vendor && \
    chown -R www-data:www-data /var/www/symfony/vendor

# Install PHP dependencies as www-data user
USER www-data
RUN composer install --no-interaction --no-scripts --prefer-dist

# Switch back to root to copy project files and set permissions
USER root

# Copy project files
COPY . .

# Ensure `bin/console` has Unix-style line endings
RUN dos2unix /var/www/symfony/bin/console

# Create var/Migrations directory if it does not exist
RUN mkdir -p /var/www/symfony/var/Migrations && \
    chown -R www-data:www-data /var/www/symfony/var/Migrations

# Ensure necessary directories have correct permissions
RUN chown -R www-data:www-data /var/www/symfony/var /var/www/symfony/vendor

# Copy PHP-FPM configuration
COPY fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Copy and fix line endings for entrypoint and wait-for-it scripts
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
COPY wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/wait-for-it && dos2unix /usr/local/bin/entrypoint.sh /usr/local/bin/wait-for-it

# Fix line endings for entrypoint.sh
RUN dos2unix /usr/local/bin/entrypoint.sh

# Switch back to the non-root user
USER www-data

# Expose port 9000 and start PHP-FPM server
EXPOSE 9000
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
