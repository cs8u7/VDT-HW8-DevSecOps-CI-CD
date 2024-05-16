FROM php:8.0-apache

RUN apt update
RUN docker-php-ext-install mysqli pdo pdo_mysql
COPY . /var/www/html
COPY ./apache-config.conf /etc/apache2/sites-available/000-default.conf
COPY ./apache-config.conf /etc/apache2/conf-available/apache-config.conf

WORKDIR /var/www/html

RUN a2enconf apache-config

RUN mkdir -p /var/www/html/upload \
    && chown -R www-data:www-data /var/www/html/upload \
    && chmod 775 /var/www/html/upload
    
EXPOSE 80
