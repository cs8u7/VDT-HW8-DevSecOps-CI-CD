#!/bin/bash

# Create upload directory with appropriate permissions
mkdir -p /var/www/html/upload
chown -R www-data:www-data /var/www/html/upload
chmod 755 /var/www/html/upload

# Start Apache
apache2-foreground
