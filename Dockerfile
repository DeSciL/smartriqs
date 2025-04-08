FROM php:8.2-apache

# Enable Apache modules for basic web serving
RUN a2enmod rewrite headers

# Create the directory for PHP files first
RUN mkdir -p /var/www/html/php

# Copy server-side PHP files - fixing the space in directory name
COPY ["server-side scripts/*.php", "/var/www/html/php/"]

# Set proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Set working directory
WORKDIR /var/www/html

EXPOSE 80
