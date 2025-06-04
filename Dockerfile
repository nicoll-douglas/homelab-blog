# Deploy stage
FROM php:8.4-apache

# Update package list
RUN apt-get update

# Set working directory
WORKDIR /var/www/html

# Change the default document root of the server
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Use production php.ini config
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy application files needed for production
COPY --chown=www-data:www-data public/ public/
COPY --chown=www-data:www-data src/ src/

# Expose Apache port
EXPOSE 80

# Switch to www-data user
USER www-data

# Start Apache
CMD ["apache2-foreground"]