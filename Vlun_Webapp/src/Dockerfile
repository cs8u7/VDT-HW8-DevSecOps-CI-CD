FROM php:8.0-apache

# Update and install necessary extensions
RUN apt-get update && apt-get install -y \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && apt-get clean

# Copy application files and Apache configuration
COPY . /var/www/html
COPY ./apache-config.conf /etc/apache2/sites-available/000-default.conf
COPY ./apache-config.conf /etc/apache2/conf-available/apache-config.conf

# Enable the custom Apache configuration
RUN a2enconf apache-config

# Copy the setup script and ensure it's executable
COPY setup.sh /usr/local/bin/setup.sh
RUN chmod +x /usr/local/bin/setup.sh

# Expose port 80
EXPOSE 80

# Use CMD to run the setup script
CMD ["/usr/local/bin/setup.sh"]
