# Use the official PHP 8.2 image as base
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    libpq-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application files
COPY . .

# Install dependencies
RUN composer install

# Set permissions
RUN chown -R www-data:www-data /var/www/html/product-backend/storage /var/www/html/product-backend/bootstrap/cache

# Set environment variables for connecting to PostgreSQL
ENV DB_CONNECTION=pgsql \
    DB_HOST=host.docker.internal \
    DB_PORT=5432 \
    DB_DATABASE=product-db \
    DB_USERNAME=postgres \
    DB_PASSWORD=root

# Expose port 8000 and start Laravel development server
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "8000"]

# [IMPORTANT]
# Redis setup (instead of password here enter your password as well as user)
RUN pecl install redis && docker-php-ext-enable redis
RUN echo "session.save_handler = redis" >> /usr/local/etc/php/php.ini
RUN echo "session.save_path = 'tcp://root:root@redis:6379'" >> /usr/local/etc/php/php.ini
