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

Il est également possible d'avoir le frontend et le backend sur le même nom DNS, en mettant le backend derrière un
préfixe dédié. Par exemple :

```
oasis.etab.fr:443 {
        tls internal

        handle_path /api/* {
                reverse_proxy localhost:8000 {
                          header_up X-Forwarded-Prefix /api
                }
        }

        @back-connect path /connect/*
        handle @back-connect {
                 reverse_proxy localhost:8000
        }

        handle {
                reverse_proxy localhost:80
        }

}
```

il faut alors configurer la variable REACT_APP_API_PREFIX à la valeur "/api".

# Lancer l'application

En mode développement :

docker compose up -d

En mode production :

docker compose -f compose.yml up -d
