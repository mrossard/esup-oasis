# Paramétrage technico-fonctionnel

En plus du paramétrage purement technique réalisé via les variables d'environnement,
l'application propose un paramétrage d'exploitation technico-fonctionnelle, stocké en base de données (et modifiable par
les administrateurs via l'api / l'interface).

## Paramètres disponibles

* **EXPEDITEUR_EMAILS** : l'expéditeur qui va apparaitre pour tous les mails automatiques générés par l'application
* **SERVICE_NOM** : nom du service en charge des étudiants à besoins spécifiques, utilisé dans certains emails ou
  documents générés par l'application
* **FREQUENCE_RAPPELS** : fréquence d'envoi des rappels des interventions à venir pour les intervenants et
  bénéficiaires (par défaut `7 days`)
* **FREQUENCE_MAJ_INSCRIPTIONS** : fréquence du traitement qui réinterroge le SI Scolarité pour récupérer les
  modifications d'inscriptions (par défaut `7 days`).
* **RAPPELS_SANS_INTERVENANTS** : détermine si on inclut les événements sans intervenant dans les rappels.(`O|N`)
* **FREQUENCE_RAPPEL_ENVOI_RH** : fréquence du traitement d'envoi du rappel de fin de période RH.(par défaut `1 day`).
* **COEFFICIENT_CHARGES** : Coefficient à appliquer pour le calcul des heures à payer aux intervenants
* **PRESIDENT_QUALITE**, **PRESIDENT_NOM** : qualité et nom du président de l'établissement, utilisés dans certains
  emails/documents.
* **RESPONSABLE_PHASE_QUALITE**, **RESPONSABLE_PHASE_NOM** : qualité et nom du responsable du service **SERVICE_NOM**,
  qui est signataire des décisions d'aménagement d'examens générées par l'application.
* **SIGNATURE_DECISIONS** : image de la signature (du responsable du service) à intégrer dans les éditions de décisions
  d'aménagements.
* **ROLES_A_JOUR** : variable purement technique permettant de forcer le recalcul des rôles applicatifs au démarrage (
  `true|false`)

## Conservation de l'historique

En plus de pouvoir être modifiés par les administrateurs via l'application, ces paramètres ont la particularité de
pouvoir conserver l'historique de leurs valeurs. Typiquement la valeur de COEFFICIENT_CHARGES peut
varier dans le temps et il est souhaitable de conserver sa valeur à l'instant observé pour éditer des documents
corrects.

Les valeurs sont donc stockées avec une date de début de validité et une date de fin de validité optionnelle, et une
bonne pratique est de ne pas écraser une valeur par une autre mais de mettre une date de fin à la valeur en cours
puis d'en ajouter une nouvelle.