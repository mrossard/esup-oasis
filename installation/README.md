# Déploiement simplifié avec Docker Compose

Cette procédure permet d'installer rapidement l'application ESUP-Oasis en utilisant Docker Compose.

## Pré-requis

- Docker et Docker Compose installés sur votre machine.

## Installation

1.  **Configurer l'environnement :**
    Copiez le fichier `.env` si nécessaire et ajustez les variables (par défaut, il est configuré pour un accès local sur les ports 8080 pour le frontend et 8000 pour le backend).

2.  **Lancer les conteneurs :**
    ```bash
    docker compose up -d
    ```

Cette commande va construire les images pour le frontend, le backend et le worker, puis lancer les services suivants :
- `oasis-postgres` : Base de données PostgreSQL.
- `oasis-gotenberg` : Service de génération de PDF.
- `oasis-backend` : API Symfony (FrankenPHP).
- `oasis-backend-worker` : Worker pour les tâches asynchrones.
- `oasis-frontend` : Interface utilisateur (React/Nginx).

## Accès

- **Frontend :** [http://localhost:8080](http://localhost:8080)
- **Backend API :** [http://localhost:8000](http://localhost:8000)

## Initialisation

Le conteneur backend exécute automatiquement les migrations et l'initialisation de la base de données au démarrage.
Les clés JWT sont également générées automatiquement si elles n'existent pas.
