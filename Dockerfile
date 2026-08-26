# -----------------------------
# Stage 1: Composer dependencies
# -----------------------------
FROM composer:2 AS build_vendor

WORKDIR /app

# Copy full Laravel project BEFORE composer install
COPY . .

RUN composer install \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    --no-progress


# -----------------------------
# Stage 2: PHP + NGINX + Laravel
# -----------------------------
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql mbstring xml

WORKDIR /var/www/html

# COPY .env.docker /var/www/html/.env    - for development, you can uncomment this line to copy the .env.docker file into the container as .env

COPY --from=build_vendor /app /var/www/html

RUN chown -R www-data:www-data storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord"]
