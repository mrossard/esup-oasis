# Déploiement simplifié avec Docker Compose

Cette procédure permet d'installer rapidement l'application ESUP-Oasis en utilisant Docker Compose.

## Pré-requis

- Docker et Docker Compose installés sur votre machine.

## Installation

1. **Configurer l'environnement :**
    - Adaptez le fichier .env global à votre installation
    - Copiez dans les dossiers installation/backend et installation/frontend les fichiers .env livrés dans les dossiers
      d'installation respectifs, et modifier les pour vos besoins.

2. **Lancer les conteneurs :**
   ```bash
   docker compose up -d
   ```

Cette commande va construire les images pour le frontend, le backend et le worker en utilisant les images pré-buildées
sur le dépot github, puis lancer les services suivants :

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

# Configuration du reverse proxy

La configuration recommandée est une séparation du frontend et backend sur deux noms DNS distincts, ce qui donnerait par
exemple avec Caddy et si vous laissez les ports par défaut :

```
backend.etab.fr {
   reverse_proxy http://localhost:8000
}
frontend.etab.fr {
   reverse_proxy http://localhost:80
}
```

Il est également possible avec une configuration plus complexe de reverse proxy d'avoir le frontend et le backend sur le
même nom DNS. Par exemple :

```
@topLevel {
   path_regexp top ^/[^/]*$
   expression {query} == ""
}

handle @topLevel {
   reverse_proxy localhost:80
}

@static path /static/*
handle @static {
   reverse_proxy localhost:80
}

handle {
   reverse_proxy localhost:8000
}
```

Ici toutes les requêtes sans paramètres qui ciblent un élément à la racine et toutes les requêtes vers /static/* sont
redirigées vers le frontend, le reste vers le backend. C'est neanmoins plus "risqué", on pourrait à l'avenir avoir
des collisions dans les chemins.
