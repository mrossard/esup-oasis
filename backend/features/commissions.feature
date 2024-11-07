# language: fr
Fonctionnalité: : Commissions
  En tant qu'administrateur je peux créer/modifier les commissions
  qui statuent sur les demandes d'accompagnement

  Scénario: Je peux obtenir la liste des commissions
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'envoie une requête "GET" sur "/commissions"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/Commission |
      | @type    | hydra:Collection     |
      | @id      | /commissions         |

  @modifyData
  Scénario: Je peux modifier une commission existante
    Etant donné que j'envoie un token d'authentification pour admin
    Et que j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Quand j'envoie une requête "PATCH" sur "/commissions/1" avec le contenu :
    """
    {
      "libelle": "lalalalala"
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/Commission |
      | @id      | /commissions/1       |
      | libelle  | lalalalala           |

  Scénario: Je ne peux pas modifier une commission si je ne suis pas admin
    Etant donné que j'envoie un token d'authentification pour gestionnaire
    Et que j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Quand j'envoie une requête "PATCH" sur "/commissions/1" avec le contenu :
    """
    {
      "libelle": "lalalalala"
    }
    """
    Alors le code de status de la réponse devrait être 403

  @modifyData
  Scénario: Je peux créer une commission
    Etant donné que j'envoie un token d'authentification pour admin
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "POST" sur "/commissions" avec le contenu :
    """
    {
      "libelle": "lalalalala",
      "actif": true
    }
    """
    Alors le code de status de la réponse devrait être 201
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/Commission |
      | @id      | /commissions/2       |
      | libelle  | lalalalala           |
    
  Scénario: Je ne peux créer une commission si je ne suis pas admin
    Etant donné que j'envoie un token d'authentification pour gestionnaire
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "POST" sur "/commissions" avec le contenu :
    """
    {
      "libelle": "lalalalala",
      "actif": true
    }
    """
    Alors le code de status de la réponse devrait être 403