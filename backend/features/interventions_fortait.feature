# language: fr
Fonctionnalité: : Interventions au forfait

  En tant qu'utilisateur avec le role ROLE_INTERVENANT
  certaines de mes interventions ne sont pas liées à des événements spécifiques
  et les utilisateurs ayant le rôle ROLE_PLANIFICATEUR peuvent les manipuler

  Scénario: En tant que bénéficiaire je ne vois pas ces interventions
    Etant donné que j'envoie un token d'authentification pour beneficiaire
    Quand j'envoie une requête "GET" sur "/interventions_forfait"
    Alors le code de status de la réponse devrait être 200
    Et le noeud JSON "hydra:totalItems" devrait contenir "0"

  Scénario: En tant qu'intervenant je vois mes interventions
    Etant donné que j'envoie un token d'authentification pour intervenant
    Quand j'envoie une requête "GET" sur "/interventions_forfait"
    Alors le code de status de la réponse devrait être 200
    Et le noeud JSON "hydra:totalItems" devrait contenir "1"

  @modifyData
  Scénario: En tant qu'utilisateur ayant le rôle ROLE_PLANIFICATEUR je peux ajouter des interventions
    Etant donné que j'envoie un token d'authentification pour renfort
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand  j'envoie une requête "POST" sur "/interventions_forfait" avec le contenu :
  """
 {
  "intervenant" : "/utilisateurs/intervenant",
  "type" : "/types_evenements/7",
  "periode" : "/periodes/3",
  "heures": "3.5"
 }
"""
    Alors le code de status de la réponse devrait être 201
    Et les noeuds JSON devraient contenir:
      | @id | /interventions_forfait/2 |
    Et le noeud JSON "dateCreation" devrait exister
    Et le noeud JSON "utilisateurCreation" devrait exister
    Et le noeud JSON "utilisateurCreation" devrait contenir "/utilisateurs/renfort"

  Scénario: En tant qu'utilisateur ayant le rôle ROLE_PLANIFICATEUR je peux filtrer les interventions par intervenant
    Etant donné que j'envoie un token d'authentification pour renfort
    Quand j'envoie une requête "GET" sur "/interventions_forfait?intervenant=/utilisateurs/intervenant"
    Alors le code de status de la réponse devrait être 200
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/InterventionForfait |
      | @type    | hydra:collection              |
    Et le noeud JSON "hydra:totalItems" ne devrait pas contenir "0"

  @modifyData
  Scénario: Je peux spécifier les bénéficiaires concernés
    Etant donné que j'envoie un token d'authentification pour renfort
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand  j'envoie une requête "POST" sur "/interventions_forfait" avec le contenu :
  """
 {
  "intervenant" : "/utilisateurs/intervenant",
  "type" : "/types_evenements/7",
  "periode" : "/periodes/3",
  "heures": "8.25",
  "beneficiaires": ["/utilisateurs/beneficiaire"]
 }
"""
    Alors le code de status de la réponse devrait être 201
    Et les noeuds JSON devraient contenir:
      | @id | /interventions_forfait/2 |
    Et le noeud JSON "dateCreation" devrait exister
    Et le noeud JSON "utilisateurCreation" devrait exister
    Et le noeud JSON "utilisateurCreation" devrait contenir "/utilisateurs/renfort"
    Et le noeud JSON "beneficiaires" devrait exister

  @modifyData
  Scénario: Je peux modifier les bénéficiaires même pour une période envoyée RH
    Etant donné que j'envoie un token d'authentification pour renfort
    Et j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Quand j'envoie une requête "PATCH" sur "/interventions_forfait/1" avec le contenu :
  """
 {
  "beneficiaires": ["/utilisateurs/beneficiaire","/utilisateurs/beneficiaire2"]
 }
"""
    Alors le code de status de la réponse devrait être 200
    Et les noeuds JSON devraient contenir:
      | @id | /interventions_forfait/1 |
    Et le noeud JSON "dateCreation" devrait exister
    Et le noeud JSON "beneficiaires[0]" devrait exister
    Et le noeud JSON "beneficiaires[1]" devrait exister