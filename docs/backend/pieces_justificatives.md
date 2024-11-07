# Pièces justificatives

Lorsqu'ils font des demandes d'accompagnement / de statut spécifique, les étudiants peuvent fournir des documents
appuyant leurs demandes. Ces documents font l'objet de traitements spécifiques dans l'application.

## Stockage

Comme mentionné dans le README, deux méthodes de stockage sont supportées par défaut :

* un stockage sur le système de fichiers du conteneur
* un stockage sur une GED Nuxéo

Dans les deux cas, en base de données ne sont stockées que les métadonnées permettant à l'application de récupérer le
fichier sur le stockage.

### Système de fichiers

Pour activer le stockage sur le système de fichiers local, il suffit de :

* ne pas renseigner la configuration Nuxeo
* renseigner le chemin d'accès à utiliser

Les fichiers envoyés seront alors stockés à l'emplacement indiqué, sous un id unique généré par l'application.

### Nuxeo

Si la configuration nuxeo est renseignée, c'est celle-ci qui sera utilisée (et ce même si vous renseignez un chemin dans
la variable `STORAGE_BASE_PATH`).

Si vous renseignez également `STORAGE_BASE_PATH`, l'application se servira du stockage fichier comme fallback en cas
d'indisponibilité de nuxeo.  
**ATTENTION** : il faudra rétablir la situation "à la main" le cas échéant, la rebascule
vers nuxeo depuis le système de fichiers n'a pas été implémentée à ce jour.

### Suppressions

Un traitement hebdomadaire s'assure de supprimer les fichiers qui ne sont plus référencés dans l'application - les
suppressions via l'interface sont de simples suppressions logiques.

## Antivirus

Quelle que soit la méthode de stockage choisie, le fichier passera par l'antivirus lors de son envoi. Il suffit pour
cela de configurer l'accès à un serveur clamav comme indiqué dans le README.

Si le serveur antivirus est temporairement indisponible, l'application ne bloque cependant pas l'envoi (présomption
d'innocence), les administrateurs techniques reçoivent par contre un email les prévenant du problème afin de leur
permettre d'intervenir.

