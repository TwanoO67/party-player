.PHONY: help start stop restart logs build clean

help:
	@echo "Party Player - Commandes disponibles:"
	@echo ""
	@echo "  make start     - Démarrer l'application"
	@echo "  make stop      - Arrêter l'application"
	@echo "  make restart   - Redémarrer l'application"
	@echo "  make logs      - Voir les logs en temps réel"
	@echo "  make build     - Reconstruire l'image Docker"
	@echo "  make clean     - Arrêter et supprimer tous les conteneurs/volumes"
	@echo ""

start:
	@echo "🚀 Démarrage de Party Player..."
	docker-compose up -d
	@echo "✅ Party Player est maintenant accessible sur http://localhost:8080"

stop:
	@echo "🛑 Arrêt de Party Player..."
	docker-compose down
	@echo "✅ Party Player a été arrêté"

restart:
	@echo "🔄 Redémarrage de Party Player..."
	docker-compose restart
	@echo "✅ Party Player a été redémarré"

logs:
	@echo "📋 Logs de Party Player (Ctrl+C pour quitter)..."
	docker-compose logs -f

build:
	@echo "🔨 Reconstruction de l'image Docker..."
	docker-compose build --no-cache
	@echo "✅ Image reconstruite avec succès"

clean:
	@echo "⚠️  ATTENTION: Cette commande va supprimer TOUS les conteneurs, images et volumes!"
	@echo "Les playlists seront supprimées!"
	@read -p "Êtes-vous sûr? (y/N) " confirm; \
	if [ "$$confirm" = "y" ] || [ "$$confirm" = "Y" ]; then \
		docker-compose down -v; \
		docker system prune -f; \
		echo "✅ Nettoyage terminé"; \
	else \
		echo "❌ Annulé"; \
	fi
