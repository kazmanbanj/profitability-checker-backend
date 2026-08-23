FROM richarvey/nginx-php-fpm:1.7.2

# Copy application code
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install Composer dependencies
RUN composer install \
    --prefer-dist \
    --optimize-autoloader

# Laravel storage permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Environment variables for the base image
ENV WEBROOT /var/www/html/public
ENV RUN_SCRIPTS=1

# The base image already contains /start.sh
CMD ["/start.sh"]
