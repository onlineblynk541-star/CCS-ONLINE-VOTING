FROM php:8.2-apache

# Install MySQL extension
RUN docker-php-ext-install mysqli

# Copy files
COPY . /var/www/html/

# Enable Apache mod_rewrite (optional)
RUN a2enmod rewrite
