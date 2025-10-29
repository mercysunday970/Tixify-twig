# Use PHP 8.4 with Apache
FROM php:8.4-apache

# Install system dependencies required for PHP extensions
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    libonig-dev \
    zlib1g-dev \
    build-essential \
    && docker-php-ext-install zip pdo pdo_mysql mbstring ctype \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Copy Composer from official Composer image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]


