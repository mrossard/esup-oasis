# language: fr
Fonctionnalité: Disciplines sportives

  L'application comporte une table de référence des disciplines sportives.
  Les administrateurs peuvent la tenir à jour.

  Scénario: En tant qu'utilisateur connecté je peux obtenir la liste des disciplines
    Etant donné que j'envoie un token d'authentification pour demandeur
    Quand j'envoie une requête "GET" sur "/disciplines_sportives"
    Alors la réponse devrait être du JSON
    Et le code de status de la réponse devrait être 200
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/DisciplineSportive |
      | @type    | hydra:Collection             |
      | @id      | /disciplines_sportives       |

  @modifyData
  Scénario: En tant qu'administrateur je peux ajouter une discipline
    Etant donné que j'envoie un token d'authentification pour admin
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "POST" sur "/disciplines_sportives" avec le contenu :
    """
    {
      "libelle" : "nouvelle discipline"
    }
    """
    Alors la réponse devrait être du JSON
    Et le code de status de la réponse devrait être 201
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/DisciplineSportive |
      | libelle  | nouvelle discipline          |
    Et le noeud JSON "actif" devrait être vrai

  Scénario: En tant que simple gestionnaire je ne peux que consulter
    Etant donné que j'envoie un token d'authentification pour gestionnaire
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "POST" sur "/disciplines_sportives" avec le contenu :
    """
    {
      "libelle" : "nouvelle discipline"
    }
    """
    Alors le code de status de la réponse devrait être 403