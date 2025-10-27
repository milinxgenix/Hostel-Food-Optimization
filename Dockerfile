# Use an official PHP + Apache image
FROM php:8.2-apache

# Copy all your project files into the container
COPY . /var/www/html/

# Expose Render’s default port
EXPOSE 10000

# Change Apache to listen on Render’s port
RUN sed -i 's/80/10000/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Enable common PHP extensions (optional)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Start Apache
CMD ["apache2-foreground"]
