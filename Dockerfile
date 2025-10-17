# Use Apache + PHP
FROM php:8.2-apache

# Enable useful Apache modules
RUN a2enmod rewrite

# Copy your PHP files into the web root
COPY . /var/www/html/

# (Optional) Set correct perms
RUN chown -R www-data:www-data /var/www/html
