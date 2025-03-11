# Use PHP 8.2 FPM with Debian-based OS
FROM php:8.2-fpm

# Fix "No such file or directory" error for sources.list
RUN test -f /etc/apt/sources.list && \
    sed -i 's|http://deb.debian.org|http://mirror.isoc.org.il/pub/debian|g' /etc/apt/sources.list || echo "No sources.list file"

# Update & Install required packages
RUN apt-get update && apt-get install -y \
    libpng-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy Laravel files
COPY . .

# Set correct permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port 8000 for Laravel
EXPOSE 8000

# Run migrations & serve Laravel app
CMD sh -c "php artisan migrate:fresh --seed && php artisan serve --host=0.0.0.0 --port=8000"
