# Développement

Pour installer les dépendances et démarrer l'application en mode développement, exécutez les commandes suivantes :

```bash
yarn install
yarn start
```

Ces commandes installent tous les packages nécessaires et lancent l'application en environnement de développement local,
où elle sera accessible par défaut à l'adresse http://localhost:3000.

## Utilisation de HTTPS/SSL en mode développement

Si votre environnement nécessite une connexion sécurisée via HTTPS/SSL (par exemple, en raison de la configuration de
votre CAS - Central Authentication Service), il est important de personnaliser la variable HOST dans le fichier
package.json.

### Modifier le fichier package.json :

```
"scripts": {
    "start": "react-scripts start",
    "start:https": "sudo PORT=443 HOST='dev.esup-portail.org' HTTPS=true ./node_modules/.bin/react-scripts start",
}
```

> Remarque : `sudo` est nécessaire sur certaines configurations pour exécuter le serveur sur le port 443.

Le site de développement est alors accessible à l'adresse https://dev.esup-portail.org.

### Démarrer l'application en mode HTTPS :

Une fois la variable HOST configurée, exécutez la commande suivante pour lancer l'application en mode HTTPS :

```bash
yarn start:https
```
