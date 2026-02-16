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

# Copier les fichiers de l'application
COPY . /var/www/html/

# Créer config.php depuis l'exemple s'il n'existe pas
RUN if [ ! -f /var/www/html/config.php ]; then \
        cp /var/www/html/config.php_exemple /var/www/html/config.php; \
    fi

# Créer .htaccess depuis l'exemple s'il n'existe pas
RUN if [ ! -f /var/www/html/.htaccess ]; then \
        if [ -f /var/www/html/.htaccess_exemple ]; then \
            cp /var/www/html/.htaccess_exemple /var/www/html/.htaccess; \
        fi; \
    fi

# Définir les permissions appropriées
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Exposer le port 80
EXPOSE 80

# Démarrer Apache
CMD ["apache2-foreground"]
