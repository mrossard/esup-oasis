# Traitements asynchrones

Le backend présente deux aspects bien distincts : une interface web, présentant une API REST, mais aussi un ensemble de
traitements asynchrones utilisant les composants Symfony Messenger (https://symfony.com/doc/current/messenger.html) et
Scheduler (https://symfony.com/doc/current/scheduler.html).

## Supervisor

Les traitements asynchrones sont pris en charge par le second conteneur "worker" du backend.

Ce dernier fait tourner le gestionnaire de processus supervisor (http://supervisord.org/), qui se charge
d'avoir en permanence des processus qui consomment les messages asynchrones générés par les appels API ou la
planification (cf. la configuration supervisor dans le dossier `docker/worker`).

## Traitements concernés

Il y a deux cas d'usage dans l'application :

* les opérations dont la durée d'exécution peut affecter la réactivité de l'application et/ou dont l'exécution peut être
  différée sans impact fonctionnel :
    * les envois de mails
    * la récupération des infos de scolarité depuis Apogée lors de la création d'un intervenant
    * la gestion des erreurs techniques
    * la génération de documents complexes
* les traitements planifiés :
    * Rappels automatiques par mail :
        * Lorsque la fin d'une période RH approche (J-4)
        * Interventions/événements dans les jours suivants (cf paramétrage technico-fonctionnel)
    * Calculs quotidiens : pour des raisons de performance, certains champs calculés voient leur valeur stockée en base.
      Certains, dont la valeur dépend entre autres de la date du jour, nécessitent un recalcul quotidien :
        * les rôles applicatifs des utilisateurs
        * l'état "avis ESE" des bénéficiaires
    * MAJ des inscriptions : les inscriptions des étudiants stockées en base sont rafraichies un traitement de mise à
      jour planifiée (hebdomadaire par défaut).
    * Nettoyage / archivage : certaines données obsolètes peuvent nécessiter d'être archivées/supprimées
      périodiquement :
        * les demandes obsolètes n'ayant jamais été finalisées sont passées à l'état "refusée" lorsque la date
          d'archivage (si renseignée) de la campagne est passée, et un mail est envoyé aux étudiants pour les en
          informer.
        * les fichiers qui ont été téléversés, mais ne sont plus référencés nulle part sont supprimés du système de
          stockage. 