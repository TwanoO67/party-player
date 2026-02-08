# Party Player - Déploiement Docker

Ce guide explique comment déployer Party Player avec Docker et Docker Compose.

## 📋 Prérequis

- Docker installé (version 20.10 ou supérieure)
- Docker Compose installé (version 2.0 ou supérieure)

## 🚀 Démarrage rapide

### 1. Cloner le projet (si pas déjà fait)

```bash
git clone https://github.com/TwanoO67/party-player.git
cd party-player
```

### 2. Configuration

Le fichier `config.php` a déjà été créé avec la clé API YouTube. Si vous souhaitez ajouter vos propres clés Spotify, éditez `config.php`:

```php
define('SPOTIFY_CLIENT_ID','votre_client_id');
define('SPOTIFY_CLIENT_SECRET','votre_client_secret');
define('SPOTIFY_REDIRECT_URI','http://localhost:8080/');
```

### 3. Construire et démarrer les conteneurs

```bash
docker-compose up -d
```

Cette commande va:
- Construire l'image Docker avec PHP 8.2 + Apache
- Créer un volume pour les playlists persistantes
- Démarrer le conteneur sur le port 8080

### 4. Accéder à l'application

Ouvrez votre navigateur et accédez à:

```
http://localhost:8080
```

## 🎮 Utilisation

### Mode Player (Serveur)

Pour créer une session de jukebox:

```
http://localhost:8080/?mode=server
```

Ou via l'URL simplifiée (grâce au .htaccess):

```
http://localhost:8080/player/[votre-session-id]
```

### Mode Client (Jukebox)

Pour rejoindre une session existante:

```
http://localhost:8080/?mode=client&sessid=[session-id]
```

Ou via l'URL simplifiée:

```
http://localhost:8080/jukebox/[session-id]
```

## 🛠 Commandes Docker utiles

### Voir les logs en temps réel

```bash
docker-compose logs -f party-player
```

### Arrêter l'application

```bash
docker-compose down
```

### Arrêter et supprimer les volumes (⚠️ supprime les playlists)

```bash
docker-compose down -v
```

### Redémarrer l'application

```bash
docker-compose restart
```

### Reconstruire l'image après modification

```bash
docker-compose build
docker-compose up -d
```

### Accéder au shell du conteneur

```bash
docker exec -it party-player bash
```

## 📁 Structure des volumes

Les playlists sont stockées dans un volume Docker persistant:

- **Volume Docker**: `party-player_playlist-data`
- **Point de montage**: `/var/www/html/_playlists`

Pour sauvegarder les playlists:

```bash
docker cp party-player:/var/www/html/_playlists ./backup-playlists
```

Pour restaurer les playlists:

```bash
docker cp ./backup-playlists/. party-player:/var/www/html/_playlists/
```

## 🔧 Configuration avancée

### Changer le port

Éditez `docker-compose.yml` et modifiez la ligne `ports`:

```yaml
ports:
  - "3000:80"  # Au lieu de 8080:80
```

### Variables d'environnement PHP

Dans `docker-compose.yml`, vous pouvez ajuster:

```yaml
environment:
  - PHP_MEMORY_LIMIT=512M      # Limite mémoire PHP
  - PHP_UPLOAD_MAX_FILESIZE=100M  # Taille max upload
  - PHP_POST_MAX_SIZE=100M     # Taille max POST
```

### Mode développement avec hot-reload

Les fichiers sont montés en volumes dans `docker-compose.yml`, donc vos modifications de code sont immédiatement reflétées sans avoir à reconstruire l'image.

## 🐛 Dépannage

### Le conteneur ne démarre pas

Vérifiez les logs:

```bash
docker-compose logs party-player
```

### Port 8080 déjà utilisé

Changez le port dans `docker-compose.yml` ou arrêtez le service qui utilise le port 8080:

```bash
# Sous Windows
netstat -ano | findstr :8080

# Sous Linux/Mac
lsof -i :8080
```

### Permissions sur _playlists

Si vous avez des erreurs de permissions:

```bash
docker exec -it party-player chown -R www-data:www-data /var/www/html/_playlists
docker exec -it party-player chmod -R 775 /var/www/html/_playlists
```

### Réinitialiser complètement

```bash
docker-compose down -v
docker system prune -a
docker-compose up -d --build
```

## 🌐 Déploiement en production

### Recommandations

1. **Proxy inverse (Nginx/Traefik)**: Utilisez un proxy inverse avec SSL/TLS
2. **Clé API sécurisée**: Ne committez jamais `config.php` dans Git
3. **Variables d'environnement**: Utilisez des secrets Docker ou variables d'environnement
4. **Sauvegardes**: Automatisez la sauvegarde du volume `_playlists`
5. **Monitoring**: Ajoutez des logs et du monitoring

### Exemple avec certificat SSL (Traefik)

```yaml
version: '3.8'

services:
  party-player:
    build: .
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.party-player.rule=Host(`partyplayer.votredomaine.fr`)"
      - "traefik.http.routers.party-player.entrypoints=websecure"
      - "traefik.http.routers.party-player.tls.certresolver=letsencrypt"
```

## 📝 Notes

- La clé API YouTube est déjà configurée dans `config.php`
- Les playlists sont automatiquement sauvegardées dans un volume Docker
- Le module Apache `mod_rewrite` est activé pour les URLs simplifiées
- PHP 8.2 est utilisé pour une meilleure performance et sécurité

## 🆘 Support

Pour toute question ou problème:
- Issues GitHub: https://github.com/TwanoO67/party-player/issues
- Email: perso@weberantoine.fr
