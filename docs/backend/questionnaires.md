# Questionnaires des demandes

Les questionnaires présentés aux étudiants demandeurs sont entièrement paramétrables dans la base de données.

Il n'y a à l'heure actuelle pas d'interface d'administration dédiée à la configuration des questions/options.

## Description synthétique

Les demandes sont gérées par types (table `type_demande`).
Les types de demandes visent à obtenir un ou plusieurs profils bénéficiaires (table `type_demande_profil_beneficiaire`)
Pour chaque type, on organise des campagnes de demande (table `campagne_demande`).  
Chaque type de demande peut se décomposer en différentes étapes (tables `etape_demande`,
`etape_demande_type_demande`).  
Chaque étape contient des questions (table `question`).  
Chaque question peut proposer des options figées (tables `option_reponse`, `option_reponse_question`), qui peuvent être
automatiquement alimentées par certaines tables de référence.

Les réponses des demandeurs sont stockées de manière indépendante pour chaque campagne (i.e un même demandeur peut avoir
des réponses différentes à la même question pour une même campagne - tables `reponse`, `reponse_option_reponse`).

## Détail des tables principales

Cette section va passer en revue les tables non triviales de cette partie de l'application.

### Table type_demande

La table type_demande liste les types de demandes qui vont pouvoir être faites via l'application :

* libelle : le libellé qui sera affiché à l'étudiant dans la liste des demandes qu'il peut faire
* actif : `true|false`, passer à false fait disparaitre le type de la liste dans l'interface d'administration
* accompagnement_optionnel : `true|false`, si `true` alors la demande a uniquement pour objectif d'obtenir le profil
  associé. Les bénéficiaires ne demandant pas d'accompagnement seront masqués dans les parties de l'application gérant
  l'accompagnement.
* visibilite_limitee : `true|false`, si `true` alors seuls les gestionnaires et les administrateurs ont accès aux
  demandes de ce type ; les renforts étudiants ne les voient pas (typiquement données médicales sensibles liées aux
  handicaps).

Ces options sont modifiables dans l'interface d'administration via l'item "Campagnes de demandes", mais on ne peut pas y
créer de nouveau type.

### Table etape_demande

La table etape_demande ne comporte que 3 champs :

* libelle : le titre de la section de questionnaire. Coté interface on affiche les étapes une par une au demandeur
* ordre : ordre d'affichage de cette étape au sein du type de demande
* si_demande_accompagnement : `true|false` si `true` alors la question "souhaitez-vous être accompagné ?" est ajoutée en
  début d'étape et on ne propose les questions liées que si la réponse est "oui".

### Table question

La table `question` est celle qui nécessite le plus de configuration, elle comporte les champs suivants :

* libelle (obligatoire) : le libellé de la question qui sera affiché au demandeur
* aide (optionnel) : une aide contextuelle qui lui sera présentée, peut être formattée en html
* type_reponse (obligatoire) : le type de réponse attendu à cette question, une valeur parmi les suivantes :
    * text : champ texte simple
    * numeric : champ texte attendant une valeur numérique
    * date : champ de sélection de date
    * textarea : champ texte libre potentiellement de grande taille
    * select : sélection parmi une liste d'options prédéfinies
    * checkbox : boite à cocher
    * file : téléversement d'un fichier
    * info: question fictive, affichera le champ "aide" dans un bloc d'information dans le questionnaire
    * submit: bouton de validation du questionnaire
* obligatoire : `true/false`
* reponse_conservable : `true/false`. Si true, lors d'une nouvelle demande on recopiera la réponse précédente de
  l'étudiant à cette question s'il y en a.
* choix_multiple: `true/false`
* table_options: la table de référence dans laquelle récupérer les options à présenter / à utiliser pour valider la
  valeur saisie. Sont disponibles les valeurs suivantes :
    * typologie_handicap : les typologies de handicap, pour une liste de sélection
    * discipline_sportive : les disciplines sportives, pour une liste de sélection
    * clubs_centre_formation : les clubs sportifs ayant le témoin "centre de formation", pour une liste de sélection
    * clubs_professionnels : les clubs sportifs ayant le témoin "professionnel", pour une liste de sélection
    * discipline_artistique: les disciplines artistiques, pour une liste de sélection
    * etablissement_artistique : les établissements d'enseignement artistique, pour une liste de sélection
    * categorie_amenagement_pedagogique : les catégories contenant au moins un type d'aménagement avec le témoin "
      pédagogique", pour une liste de sélection
    * categorie_amenagement_examens : les catégories contenant au moins un type d'aménagement avec le témoin "
      examens", pour une liste de sélection
    * sportif_haut_niveau : la liste des sportifs de haut niveau, pour validation du numéro PSQS saisi.
* champ_cible: le champ à renseigner automatiquement dans le dossier bénéficiaire à partir de la réponse à cette
  question, une fois la demande acceptée. Sont pris en charge :
    * contact_urgence : le contact d'urgence du bénéficiaire
    * email_perso : l'adresse email personnelle du bénéficiaire
    * tel_perso : le numéro de téléphone personnel du bénéficiaire

### Tables option_reponse / option_reponse_question

On retrouve ici les options proposées pour des questions de type `select` non liées à une table de référence :

* libelle : le libelle à afficher
* question_id : l'identifiant de la question pour laquelle cette option est proposée

Vous pouvez également déclencher l'ajout de nouvelles questions liées si le demandeur choisit cette option, via la table
`option_reponse_question`, qui contient simplement l'id de l'option_reponse et l'id de la question à ajouter quand l'
option est choisie.

**ATTENTION** Cette table doit comporter deux lignes aux ids / valeurs figées pour la gestion du champ
`si_demande_accompagnement` de la table etape_demande :

* l'id -1 est l'option "réponse oui à la question 'souhaitez-vous être accompagné?'"
* l'id -2 est l'option "réponse non à la question 'souhaitez-vous être accompagné?'"

## Chargement initial

Afin de faciliter la création initiale du questionnaire sans interface, une commande permettant de charger les données
préparées sous forme de fichiers YAML a été préparée.
Dans le dossier `fixtures/questionnaire` vous trouverez le questionnaire tel qu'il a été mis en place dans notre
établissement, sous la forme de fixtures Alice (https://github.com/nelmio/alice).

Pour charger ces données dans une base ne contenant pas encore de questionnaire, il suffit de lancer la commande
suivante dans le conteneur :

```
$ php bin/console app:init-questionnaire
```