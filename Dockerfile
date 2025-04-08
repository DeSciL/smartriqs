FROM php:8.2-apache

# Enable Apache modules
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Create necessary directories
RUN mkdir -p /var/www/html/php
RUN mkdir -p /var/www/html/php/test_researcher1
RUN mkdir -p /var/www/html/php/test_researcher1/test_study1_chat_logs/Group_1
RUN mkdir -p /var/www/html/php/test_researcher2

# Copy server-side PHP files
COPY ["server-side scripts/*.php", "/var/www/html/"]

# Set proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

# Add startup message
CMD apache2-foreground
