# Use an official PHP 8.1 FPM image as a parent image
FROM php:8.1-fpm

# Set the working directory to /app
WORKDIR /app

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Symfony CLI manually using curl
RUN curl -sS https://get.symfony.com/cli/symfony.phar -o /usr/local/bin/symfony && chmod +x /usr/local/bin/symfony

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
