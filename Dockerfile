# Stage 1 - Build Frontend (Vite)
FROM node:18 AS frontend
WORKDIR /app

# Copy only package files first to leverage Docker cache
COPY package*.json ./
RUN npm install

# Copy the rest of the frontend files
COPY . .

# Build frontend assets
RUN npm run build

# Stage 2 - Backend (Laravel + PHP + Composer)
FROM php:8.2-fpm AS backend

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl unzip libonig-dev libzip-dev zip sqlite3 \
    && docker-php-ext-install pdo pdo_mysql mbstring zip pdo_sqlite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy Laravel backend files
COPY . .

# Copy built frontend from Stage 1
COPY --from=frontend /app/public/dist ./public/dist

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel setup
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan cache:clear && \
    php artisan migrate:fresh --seed

# Expose port 9000 (default for php-fpm)
EXPOSE 9000

CMD ["php-fpm"]
