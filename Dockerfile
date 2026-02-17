# Stage 1: Build frontend
FROM node:20-alpine AS frontend-build

WORKDIR /app/frontend
COPY frontend/package.json frontend/package-lock.json ./
RUN npm ci
COPY frontend/ ./
RUN npm run build

# Stage 2: PHP + Apache with built frontend
FROM php:8.4-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*

# Activer les modules Apache nécessaires
RUN a2enmod rewrite headers expires

# Créer le dossier pour les playlists avec les bonnes permissions
RUN mkdir -p /var/www/html/_playlists && \
    chown -R www-data:www-data /var/www/html/_playlists && \
    chmod -R 775 /var/www/html/_playlists

# Configuration Apache pour permettre .htaccess
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/party-player.conf && \
    a2enconf party-player

# Copier les fichiers backend PHP
COPY api.php /var/www/html/
COPY api/ /var/www/html/api/
COPY .htaccess /var/www/html/
COPY config.php_exemple /var/www/html/config.php_exemple

# Copier le frontend buildé directement à la racine du DocumentRoot
COPY --from=frontend-build /app/dist/ /var/www/html/

# Créer config.php depuis l'exemple s'il n'existe pas
RUN if [ ! -f /var/www/html/config.php ]; then \
        cp /var/www/html/config.php_exemple /var/www/html/config.php; \
    fi

# Définir les permissions appropriées
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Exposer le port 80
EXPOSE 80

# Démarrer Apache
CMD ["apache2-foreground"]
