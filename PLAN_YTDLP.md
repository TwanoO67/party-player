# Plan : Remplacement YouTube IFrame par yt-dlp + lecteur HTML5

## Contexte

Certaines vidéos YouTube sont bloquées en embed via l'IFrame Player (restriction d'embed, contenu protégé). On remplace le player YouTube par un lecteur HTML5 natif alimenté par yt-dlp côté serveur, qui extrait les URLs de stream directes depuis le CDN Google. Pas de cache — chaque lecture déclenche une extraction fraîche.

## Étapes

### 1. Installer yt-dlp dans le Dockerfile

**Fichier** : `Dockerfile`

- Ajouter `python3` et `yt-dlp` dans le stage PHP/Apache
- `RUN apt-get install -y python3 python3-pip && pip3 install yt-dlp`

### 2. Créer l'endpoint PHP d'extraction de stream

**Fichier** : `api/stream.php` (nouveau)

- Reçoit `?id=VIDEO_ID`
- Exécute `yt-dlp -f "bestaudio[ext=m4a]/bestaudio" -g --no-playlist "https://www.youtube.com/watch?v=VIDEO_ID"`
- Retourne JSON : `{ "url": "https://...googlevideo.com/..." }`
- Gestion d'erreur si yt-dlp échoue (vidéo supprimée, privée, etc.)
- Option : paramètre `?format=video` pour récupérer vidéo+audio au lieu d'audio seul

### 3. Ajouter le proxy dans le .htaccess

**Fichier** : `.htaccess`

- Ajouter `RewriteRule ^api/(.*)$ api/$1 [L]` (déjà présent, vérifié)

### 4. Créer le service frontend

**Fichier** : `frontend/src/services/stream.ts` (nouveau)

```ts
export async function getStreamUrl(videoId: string, format: 'audio' | 'video' = 'video'): Promise<string>
```

- `fetch('/api/stream.php?id=VIDEO_ID&format=video')`
- Retourne l'URL du stream

### 5. Remplacer le composable YouTube Player

**Fichier** : `frontend/src/composables/useYouTubePlayer.ts` (modifier)

- Remplacer l'IFrame YouTube par un élément `<video>` HTML5 natif
- `loadVideo(videoId)` : appelle `getStreamUrl()`, puis set `video.src = url`
- Garder les mêmes events : `onEnded`, `onPlaying`, `onPaused`
- Garder le progress tracking via `video.currentTime` / `video.duration`

### 6. Adapter le composant VideoPlayer

**Fichier** : `frontend/src/components/server/VideoPlayer.vue` (modifier)

- Remplacer le div `#yt-player` par un `<video>` HTML5 avec contrôles natifs
- Conserver l'habillage CRT/rétro (scanlines, glow)
- Exposer `loadVideo()` au parent

### 7. Mettre à jour le docker-compose

**Fichier** : `docker-compose.yml`

- Monter `api/stream.php` si nécessaire (déjà monté via `./api:/var/www/html/api`)

## Fichiers impactés

| Fichier | Action |
|---------|--------|
| `Dockerfile` | Ajouter python3 + yt-dlp |
| `api/stream.php` | Créer — endpoint extraction stream |
| `frontend/src/services/stream.ts` | Créer — appel API stream |
| `frontend/src/composables/useYouTubePlayer.ts` | Réécrire — HTML5 au lieu de YT IFrame |
| `frontend/src/components/server/VideoPlayer.vue` | Adapter — `<video>` au lieu de div YT |

## Vérification

1. `docker compose build && docker compose up`
2. Ajouter une vidéo à la playlist depuis le client
3. La vidéo se lance automatiquement côté serveur en HTML5
4. Tester avec une vidéo qui était bloquée en embed — doit maintenant fonctionner
5. Vérifier next/prev track, auto-play du suivant
6. Vérifier que les contrôles (play/pause/volume) fonctionnent
