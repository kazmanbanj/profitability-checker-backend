# -----------------------------
# Stage 1: Composer dependencies
# -----------------------------
FROM composer:2 AS composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    --no-progress

COPY . .
RUN composer dump-autoload --optimize


# -----------------------------
# Stage 2: PHP + NGINX + Laravel
# -----------------------------
FROM php:8.2-fpm

# Install system packages
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring xml

# Copy Laravel app
COPY --from=composer /app /var/www/html
WORKDIR /var/www/html

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Copy NGINX config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy Supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord"]
