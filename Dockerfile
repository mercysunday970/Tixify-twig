# Use an official PHP image with necessary extensions
FROM php:8.2-apache

# Enable Apache mod_rewrite (needed for clean URLs)
RUN a2enmod rewrite

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Copy project files to Apache document root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    rm composer-setup.php

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Give proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose Render’s dynamic port (Render injects $PORT)
EXPOSE 10000

# Start Apache listening on Render's $PORT
CMD sed -i "s/80/\${PORT}/g" /etc/apache2/ports.conf && apache2-foreground
