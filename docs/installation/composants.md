# Composants de l'installation "tout-en-un"

L'installation "tout-en-un" comprend les composants suivants :

* le frontend
* le backend
* le serveur de base de données
* Un reverse proxy caddy

En activant le mode test/développement, on a également un service Mailhog (smtp fictif).

## Le reverse proxy caddy

Son rôle est de distribuer les requêtes HTTP vers le backend et le frontend. Par convention, nous avons choisi de mettre
le frontend à la racine et le backend sur l'endpoint /api.

Vous aurez donc l'interface utilisateur accessible directement à l'url https://votre-domaine-oasis, et l'interface
swagger ui du backend à l'url https://votre-domaine-oasis/api/.

Si vous souhaitez utiliser des noms de domaines différents, modifier l'endpoint du backend... vous pouvez modifier le
fichier [Caddyfile](../../installation/caddy/Caddyfile) présent dans le
dossier [installation/caddy](../../installation/caddy) pour l'adapter à votre installation. N'oubliez pas dans ce cas
d'adapter également les variables d'environnement dans le fichier .env pour refléter vos choix (FRONT_URL, BACK_URL,
REACT_APP_API_PREFIX...).

Si vous disposez déjà d'un reverse proxy configuré pour vos besoins, vous pouvez simplement retirer la section dédiée
à Caddy dans le fichier [compose.yaml](../../installation/compose.yaml).

## Le serveur de bases de données

Oasis utilise postgresql. On vous propose ici une installation locale, mais vous pouvez aussi utiliser un serveur
externe. Dans ce cas, vous devrez adapter les variables d'environnement dans le fichier .env pour refléter vos choix.

Au lieu des variables POSTGRES_* proposées dans le fichier .env.exemple, vous pouvez renseigner directement une variable
DATABASE_URL telle qu'attendue par le backend (postgres://user:password@host:port/dbname).

> [!CAUTION]
> Si vous conservez l'installation locale, n'oubliez pas les sauvegardes ! Vous pouvez utiliser les commandes classiques
> disponibles pour postgres via docker, par exemple pour un dump complet :
> ```
> docker exec -t oasis-postgres pg_dump -U app -d app > /tmp/backup.sql
> ```