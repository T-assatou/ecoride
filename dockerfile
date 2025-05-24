FROM php:8.2-apache

# Installe les outils système nécessaires à Composer (zip, unzip, git)
RUN apt-get update && apt-get install -y \
    zip unzip git && rm -rf /var/lib/apt/lists/*

# Installe les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Installe Composer dans l'image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Active mod_rewrite
RUN a2enmod rewrite

# Configure Apache pour écouter sur le port 8080
EXPOSE 8080
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf && \
    sed -i 's/80/8080/g' /etc/apache2/sites-available/000-default.conf

# Copie le projet
COPY . /var/www/html/
WORKDIR /var/www/html/

# Installe les dépendances PHP
RUN composer install --no-dev --no-interaction --prefer-dist

# Lancement d'Apache
CMD ["apache2-foreground"]