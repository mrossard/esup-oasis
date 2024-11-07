# Authentification

L'API est entièrement privée, les seuls endpoints publics sont ceux permettant de s'authentifier, derrière le préfixe
`/connect`.

## Authentification d'un utilisateur

Le frontend déclenche de son côté l'authentification auprès du serveur OAuth, et obtient en retour un accesstoken.
Il obtient ensuite un token JWT du backend en appelant l'endpoint `/connect/oauth/token` et en lui passant l'accesstoken
obtenu - la requête curl correspondante serait :

```
$ curl 'https://backend.univ.fr/connect/oauth/token?json=1' \ 
    -X POST \
    -H 'Content-Type: application/json' \
    -H 'Connection: keep-alive' \
    -data-raw '{"accessToken":"AT-xxxxxxxxxxxxxxxxxxxxx"}'
```

En retour l'API va :

* créer un cookie de session httpOnly "oasis-token" contenant le JWT généré
* retourner ce même token dans le payload de la réponse :

  ```
  {"token":"eyxxxxxxxxxxxxxxxxxxxxxxxxx"}
  ```

Les appels suivants à l'API devront fournir le JWT obtenu soit via le cookie "oasis-token", soit dans le header
HTTP Authorization (`Authorization: Bearer eyxxxxxxxxxxxxxx`)

## Authentification d'une application cliente

En complément d'une authentification par un utilisateur via OAuth, l'API permet des accès (très restreints) par une
application externe via un identifiant d'application cliente et une clé d'API.

Pour obtenir un token JWT, les applications clientes peuvent appeler l'endpoint `/connect/apikey` :

```
$ curl 'https://backend.univ.fr/connect/apikey' \ 
    -X POST \
    -H 'Content-Type: application/json' \
    -H 'Connection: keep-alive' \
    -data-raw '{"appId":"identifiant", "apiKey":"apiKey"}'
```

Comme pour l'authentification d'un utilisateur classique, les appels suivants devront fournir le JWT obtenu dans les
headers de la requête.

Le seul endpoint actuellement autorisé pour ce type de client est /evenements. Il est utilisé dans une application
locale pour générer des calendriers individuels.

## Authentification complète sans frontend

Pour le développement il peut être pratique de s'authentifier directement depuis le backend, sans avoir de frontend
déployé. L'endpoint suivant a été mis en place à cet effet : `/connect/oauth`; en y accédant directement depuis un
navigateur vous serez redirigé vers le serveur Oauth, puis authentifié coté application (cookie httpOnly positionné +
token retourné).