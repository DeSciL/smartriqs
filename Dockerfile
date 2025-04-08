FROM php:8.2-apache

# Enable Apache modules
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Copy server-side files
COPY ["server-side scripts/*", "/var/www/html/"]

# Set proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

# Add startup message
CMD apache2-foreground
