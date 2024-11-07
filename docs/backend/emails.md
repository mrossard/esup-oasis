# Emails

L'application envoie de nombreux emails au fil du traitement des dossiers, des interventions et du calendrier RH.  
Ils sont formatés via des templates twig ( https://twig.symfony.com/ ) à modifier / adapter à vos besoins.  
Vous les trouverez dans le dossier `templates/mail` :

* **base.html.twig** : le cadre de base pour tous les mails, qui sera surchargé par les autres pour y ajouter le contenu
  concret.
* **bienvenueIntervenant.html.twig** : mail de bienvenue et d'information sur la plateforme envoyé aux nouveaux
  intervenants lorsqu'ils sont créés.
* **rappelEnvoiRH.html.twig** : rappel envoyé aux gestionnaires 4 jours avant la clôture d'une période RH, les invitant
  à vérifier que les événements concernés par cette période sont bien saisis (pour paiement des intervenants).
* **rappelValidationInterventionsRenforts.html.twig** : rappel envoyé aux gestionnaires 4 jours avant la clôture d'une
  période RH, les invitant à valider/annuler les interventions saisies par leurs renforts pour cette période.
* **rappelIntervenant.html.twig** et **rappelBeneficiaire.html.twig** : rappels hebdomadaires envoyés respectivement aux
  intervenants et aux bénéficiaires, récapitulant les événements les concernant pour la semaine à venir.
* **demande[...].html.twig** : emails de confirmation / information envoyés aux demandeurs au fil du traitement de leurs
  demandes
* **decisionAmenagement.html.twig** : email contenant en pièce jointe la décision d'aménagement d'examens en PDF.
* **erreurTechnique.html.twig** : email envoyé aux administrateurs techniques en cas d'erreur technique détectée (par
  exemple indisponibilité de l'antivirus)
* **rapportMajInsciptions.html.twig** : rapport succinct du traitement hebdomadaire de mise à jour des informations de
  scolarité
* **rapportNettoyage.html.twig** : rapporœt succinct du traitement de nettoyage des fichiers non utilisés