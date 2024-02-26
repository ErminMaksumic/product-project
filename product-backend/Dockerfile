# Use the official PHP image with Apache
FROM php:8.2-apache

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql

# Set the working directory in the container
WORKDIR /var/www/html

# Copy the application files to the container
COPY . /var/www/html

# Install Composer dependencies
RUN composer install --no-dev --no-interaction --prefer-dist

# Set the appropriate permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Expose port 80 to the outside
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
