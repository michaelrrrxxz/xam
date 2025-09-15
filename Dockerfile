# Stage 1 - Node (for npm install only)
FROM node:18 AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .

# Stage 2 - PHP / Laravel Backend
FROM php:8.2-fpm

WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl unzip libonig-dev libzip-dev zip sqlite3 \
    && docker-php-ext-install pdo pdo_mysql mbstring zip pdo_sqlite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy app files
COPY . .

# Copy node_modules if needed (optional, skip build for testing)
COPY --from=frontend /app/node_modules ./node_modules

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel setup
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan cache:clear && \
    php artisan migrate:fresh --seed

# Expose port if needed
EXPOSE 9000

CMD ["php-fpm"]
