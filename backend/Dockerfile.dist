FROM php:8.2-fpm

# Switch to root user for installation
USER root

# Install system dependencies and PHP extensions
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
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql intl zip opcache gd bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

# Copy wait-for-it script
COPY wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod +x /usr/local/bin/wait-for-it

# Set working directory
WORKDIR /var/www/symfony

# Copy project files
COPY . .

# Set permissions for the application files
RUN chown -R www-data:www-data /var/www/symfony

# Create Composer cache directory and set permissions
RUN mkdir -p /var/www/.composer/cache && \
    chown -R www-data:www-data /var/www/.composer

# Copy entrypoint script and set permissions
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Install Composer dependencies
RUN composer install --no-interaction --prefer-dist --no-scripts --optimize-autoloader --no-dev

# Switch back to the non-root user
USER www-data

# Expose port 9000 and start PHP-FPM server
EXPOSE 9000
CMD ["php-fpm"]
