# Dockerfile
FROM php:8.2-apache

# Copie les fichiers dans le dossier /var/www/html
COPY . /var/www/html/

# Active mod_rewrite pour Apache
RUN a2enmod rewrite

# Configure les droits (optionnel)
RUN chown -R www-data:www-data /var/www/html

# Expose le port
EXPOSE 80