FROM php:8.2-apache

# Enable mysqli
RUN docker-php-ext-install mysqli

# Copy files to apache root
COPY . /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html

# Enable rewrite (important for PHP apps)
RUN a2enmod rewrite

EXPOSE 80
