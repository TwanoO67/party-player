@echo off
chcp 65001 >nul
echo.
echo 🎉 Party Player - Démarrage avec Docker
echo ========================================
echo.

REM Vérifier si Docker est installé
docker --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Docker n'est pas installé. Veuillez installer Docker Desktop d'abord.
    echo    https://www.docker.com/products/docker-desktop
    pause
    exit /b 1
)

REM Vérifier si Docker Compose est installé
docker-compose --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Docker Compose n'est pas installé.
    pause
    exit /b 1
)

echo ✅ Docker et Docker Compose sont installés
echo.

REM Construire et démarrer les conteneurs
echo 🔨 Construction de l'image Docker...
docker-compose build

echo.
echo 🚀 Démarrage du conteneur...
docker-compose up -d

echo.
echo ✅ Party Player est maintenant en cours d'exécution!
echo.
echo 📍 Accédez à l'application sur: http://localhost:8080
echo.
echo 🎮 Modes disponibles:
echo    - Mode Player: http://localhost:8080/?mode=server
echo    - Mode Client: http://localhost:8080/?mode=client^&sessid=[session-id]
echo.
echo 📋 Commandes utiles:
echo    - Voir les logs:     docker-compose logs -f
echo    - Arrêter:           docker-compose down
echo    - Redémarrer:        docker-compose restart
echo.
pause
