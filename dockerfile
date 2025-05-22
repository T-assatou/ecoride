# Utilise une image officielle PHP avec Apache
FROM php:8.1-apache

# Active les extensions nécessaires (si tu utilises MySQL)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copie tout le contenu dans le dossier web
COPY . /var/www/html/

# Active le module rewrite d'Apache (utile pour certaines redirections)
RUN a2enmod rewrite