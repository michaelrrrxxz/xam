# Stage 1 - Node (for npm install only)
FROM node:18 AS frontend

WORKDIR /app

# Copy package files and install dependencies
COPY package*.json ./
RUN npm install

# Copy the rest of the frontend files
COPY . .

# Stage 2 - PHP / Laravel Backend
FROM php:8.2-fpm

WORKDIR /var/www

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libonig-dev \
    libzip-dev \
    zip \
    sqlite3 \
    libsqlite3-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    libxslt1-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        pdo_sqlite \
        bcmath \
        gd \
        intl \
        xml \
        xsl \
        curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Copy node_modules from frontend stage
COPY --from=frontend /app/node_modules ./node_modules

# Set proper permissions for Laravel
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Install PHP dependencies
USER www-data
RUN composer install --no-dev --optimize-autoloader
USER root

# Laravel setup
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan cache:clear && \
    php artisan migrate:fresh --seed

# Expose port 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
