# language: fr
Fonctionnalité: Utilisateurs

  En tant qu'utilisateur authentifié
  Je peux intervenir sur /utilisateurs en fonction de mes rôles.

  Scénario: En tant que renfort je peux liste les utilisateurs par rôle
    Etant donné que j'envoie un token d'authentification pour renfort
    Quand j'envoie une requête "GET" sur "/roles/ROLE_PLANIFICATEUR/utilisateurs"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/Utilisateur                  |
      | @type    | hydra:Collection                       |
      | @id      | /roles/ROLE_PLANIFICATEUR/utilisateurs |

  Scénario: En tant qu'admin je peux lister les utilisateurs ayant le ROLE_ADMIN
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'envoie une requête "GET" sur "/roles/ROLE_ADMIN/utilisateurs"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/Utilisateur          |
      | @type    | hydra:Collection               |
      | @id      | /roles/ROLE_ADMIN/utilisateurs |

  Scénario: En tant qu'admin je peux paginer la liste
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'envoie une requête "GET" sur "/roles/ROLE_INTERVENANT/utilisateurs?itemsPerPage=1&page=2"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et les noeuds JSON devraient contenir:
      | @context         | /contexts/Utilisateur                |
      | @type            | hydra:Collection                     |
      | @id              | /roles/ROLE_INTERVENANT/utilisateurs |
      | hydra:totalItems | 2                                    |
      | hydra:view.@type | hydra:PartialCollectionView          |
    Et le noeud JSON "hydra:member[0]" devrait exister
    Et le noeud JSON "hydra:member[1]" ne devrait pas exister

  Scénario: En tant que gestionnaire je ne peux pas lister les utilisateurs ayant le ROLE_ADMIN
    Etant donné que j'envoie un token d'authentification pour gestionnaire
    Quand j'envoie une requête "GET" sur "/roles/ROLE_ADMIN/utilisateurs"
    Alors le code de status de la réponse devrait être 403

  Scénario: En tant qu'admin je peux lister les bénéficiaires
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'envoie une requête "GET" sur "/beneficiaires"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "hydra:member" devrait exister
    Et le noeud JSON "hydra:totalItems" devrait être égal à "2"
    Et le noeud JSON "hydra:member[0].@id" devrait être égal à "/utilisateurs/beneficiaire"
    Et le noeud JSON "hydra:member[0].profils" devrait exister
    Et le noeud JSON "hydra:member[0].gestionnairesActifs" devrait exister

  Scénario: En tant que gestionnaire je vois les inscriptions des bénéficiaires
    Etant donné que j'envoie un token d'authentification pour gestionnaire
    Quand j'envoie une requête "GET" sur "/beneficiaires"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "hydra:member" devrait exister
    Et le noeud JSON "hydra:totalItems" devrait être égal à "2"
    Et le noeud JSON "hydra:member[0].inscriptions" devrait avoir 1 élément
    Et le noeud JSON "hydra:member[0].inscriptions[0].debut" devrait exister

  Scénario: En tant que gestionnaire je peux voir si un intervenant n'a pas d'inscriptions
    Etant donné que j'envoie un token d'authentification pour gestionnaire
    Quand j'envoie une requête "GET" sur "/intervenants"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "hydra:member" devrait exister
    Et le noeud JSON "hydra:member[0].inscriptions" devrait avoir 0 éléments

    ## 2 intervenants + 1 renfort!
  Scénario: En tant qu'admin je peux lister les intervenants
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'envoie une requête "GET" sur "/intervenants?order[nom]=asc"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "hydra:member" devrait exister
    Et le noeud JSON "hydra:totalItems" devrait être égal à "3"
    Et le noeud JSON "hydra:member[0].@id" devrait être égal à "/utilisateurs/intervenant"
    Et le noeud JSON "hydra:member[1].@id" devrait être égal à "/utilisateurs/intervenant2"
    Et le noeud JSON "hydra:member[2].@id" devrait être égal à "/utilisateurs/renfort"

  Scénario: En tant qu'admin je peux chercher les intervenants par nom et ne pas obtenir de résultat
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'envoie une requête "GET" sur "/intervenants?nom=toto"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "hydra:totalItems" devrait être égal à "0"

  Scénario: En tant qu'admin je peux chercher les intervenants par nom et obtenir des résultats
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'envoie une requête "GET" sur "/intervenants?nom=inter"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "hydra:totalItems" devrait être égal à "2"

  Scénario: En tant qu'utilisateur avec ROLE_PLANIFICATEUR j'ai accès à l'email perso
    Etant donné que j'envoie un token d'authentification pour renfort
    Quand j'envoie une requête "GET" sur "/utilisateurs/intervenant"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "emailPerso" devrait exister

  @modifyData
  Scénario: En tant qu'admin je peux ajouter des intervenants
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Et j'envoie une requête "PATCH" sur "/utilisateurs/nouvelintervenant" avec le contenu :
  """
    {
    "roles":["ROLE_INTERVENANT"],
    "typesEvenements": ["/types_evenements/1"],
    "campus": ["/campus/1"],
    "competences":["/competences/1"]
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/Utilisateur           |
      | @type    | Utilisateur                     |
      | @id      | /utilisateurs/nouvelintervenant |
    Et le noeud JSON "roles[0]" devrait contenir "ROLE_USER"
    Et le noeud JSON "roles[1]" devrait contenir "ROLE_INTERVENANT"

  @modifyData
  Scénario: Un intervenant doit être lié à au moins un type d'événements
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Et j'envoie une requête "PATCH" sur "/utilisateurs/nouvelintervenant" avec le contenu :
  """
    {
    "roles":["ROLE_INTERVENANT"],
    "typesEvenements": [],
    "campus": ["/campus/1", "/campus/2"],
    "competences":["/competences/1", "/competences/3"]
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/Utilisateur           |
      | @type    | Utilisateur                     |
      | @id      | /utilisateurs/nouvelintervenant |
    Et le noeud JSON "roles[0]" devrait contenir "ROLE_USER"
    Et le noeud JSON "roles[1]" ne devrait pas exister


  Scénario: En tant qu'admin je peux chercher les bénéficiaires par nom et ne pas obtenir de résultat
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'envoie une requête "GET" sur "/beneficiaires?nom=toto"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "hydra:totalItems" devrait être égal à "0"

  Scénario: En tant qu'admin je peux chercher les bénéficiaires par nom et obtenir des résultats
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'envoie une requête "GET" sur "/beneficiaires?nom=beneficiaire2"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "hydra:totalItems" devrait être égal à "1"

  Scénario: En tant que planificateur je peux rechercher les bénéficiaires par (nom, prénom, uid, email)
    Etant donné que j'envoie un token d'authentification pour renfort
    Quand j'envoie une requête "GET" sur "/beneficiaires?recherche=benef"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "hydra:totalItems" devrait être égal à "2"

  Scénario: En tant que planificateur je peux rechercher les intervenants par (nom, prénom, uid, email)
    Etant donné que j'envoie un token d'authentification pour renfort
    Quand j'envoie une requête "GET" sur "/intervenants?recherche=intér"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "hydra:member[0].@id" devrait contenir "/utilisateurs/intervenant"
    Et le noeud JSON "hydra:member[1].@id" devrait contenir "/utilisateurs/intervenant2"
    Et le noeud JSON "hydra:member[2]" ne devrait pas exister

  @modifyData
  Scénario: En tant qu'admin je peux ajouter des gestionnaires
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Et j'envoie une requête "PATCH" sur "/utilisateurs/nouveaugestionnaire" avec le contenu :
  """
    {
    "roles":["ROLE_GESTIONNAIRE"],
    "services": ["/services/1"]
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "roles[0]" devrait contenir "ROLE_USER"
    Et le noeud JSON "roles[1]" devrait contenir "ROLE_PLANIFICATEUR"
    Et le noeud JSON "roles[2]" devrait contenir "ROLE_GESTIONNAIRE"

  @modifyData
  Scénario: En tant qu'admin je peux modifier les gestionnaires
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Et j'envoie une requête "PATCH" sur "/utilisateurs/gestionnaire" avec le contenu :
  """
    {
    "services": ["/services/2", "/services/3"]
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "services[0]" devrait contenir "/services/2"
    Et le noeud JSON "services[1]" devrait contenir "/services/3"

  @modifyData
  Scénario: En tant qu'admin je peux ajouter des renforts
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Et j'envoie une requête "PATCH" sur "/utilisateurs/nouveaurenfort" avec le contenu :
  """
    {
    "roles":["ROLE_RENFORT"],
    "services": ["/services/1"]
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "roles[0]" devrait contenir "ROLE_USER"
    Et le noeud JSON "roles[1]" devrait contenir "ROLE_PLANIFICATEUR"
    Et le noeud JSON "roles[2]" devrait contenir "ROLE_RENFORT"

  @modifyData
  Scénario: Je peux attribuer à un ancien gestionnaire le rôle ROLE_RENFORT
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Et j'envoie une requête "PATCH" sur "/utilisateurs/gestionnaire2" avec le contenu :
  """
    {
    "roles":[],
    "services": ["/services/1"]
    }
    """
    Et j'envoie un token d'authentification pour admin
    Et j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Et j'envoie une requête "PATCH" sur "/utilisateurs/gestionnaire2" avec le contenu :
  """
    {
    "roles":["ROLE_RENFORT"]
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "roles[0]" devrait contenir "ROLE_USER"
    Et le noeud JSON "roles[1]" devrait contenir "ROLE_PLANIFICATEUR"
    Et le noeud JSON "roles[2]" devrait contenir "ROLE_RENFORT"

  @modifyData
  Scénario: Un renfort qui n'est plus lié à aucun service perd le rôle
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Et j'envoie une requête "PATCH" sur "/utilisateurs/nouveaurenfort" avec le contenu :
  """
    {
    "roles":["ROLE_RENFORT"],
    "services": []
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "roles[0]" devrait contenir "ROLE_USER"
    Et le noeud JSON "roles[1]" ne devrait pas exister

  Scénario: En tant que renfort je ne vois pas les profils des bénéficiaires
    Etant donné que j'envoie un token d'authentification pour renfort
    Quand j'envoie une requête "GET" sur "/utilisateurs/beneficiaire"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "profils" ne devrait pas exister

  @modifyData
  Scénario: En tant qu'admin je peux retirer le ROLE_INTERVENANT
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Et j'envoie une requête "PATCH" sur "/utilisateurs/intervenant" avec le contenu :
  """
    {
    "roles":[]
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "roles[0]" devrait contenir "ROLE_USER"
    Et le noeud JSON "roles[1]" ne devrait pas exister

  @modifyData
  Scénario:En tant qu'admin je peux réactiver le ROLE_INTERVENANT
    Etant donné que j'envoie un token d'authentification pour admin
    Et j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Et j'envoie une requête "PATCH" sur "/utilisateurs/intervenant" avec le contenu :
  """
    {
    "roles":[]
    }
    """
    Quand  j'envoie un token d'authentification pour admin
    Et j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Et j'envoie une requête "PATCH" sur "/utilisateurs/intervenant" avec le contenu :
  """
    {
    "roles":["ROLE_INTERVENANT"]
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et le noeud JSON "roles[0]" devrait contenir "ROLE_USER"
    Et le noeud JSON "roles[1]" devrait contenir "ROLE_INTERVENANT"

  @modifyData
  Scénario: En tant que gestionnaire je peux donner le ROLE_INTERVENANT
    Etant donné que j'envoie un token d'authentification pour gestionnaire
    Quand j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Et j'envoie une requête "PATCH" sur "/utilisateurs/nouvelintervenant" avec le contenu :
  """
    {
    "roles":["ROLE_INTERVENANT"],
    "typesEvenements": ["/types_evenements/1"],
    "campus": ["/campus/2"],
    "competences":["/competences/3"]
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/Utilisateur           |
      | @type    | Utilisateur                     |
      | @id      | /utilisateurs/nouvelintervenant |
    Et le noeud JSON "roles[0]" devrait contenir "ROLE_USER"
    Et le noeud JSON "roles[1]" devrait contenir "ROLE_INTERVENANT"


  Scénario: Un renfort a ROLE_RENFORT et ROLE_PLANIFICATEUR
    Etant donné que j'envoie un token d'authentification pour admin
    Quand j'envoie une requête "GET" sur "/utilisateurs/renfort"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et l'entête "Content-Type" devrait être égal à "application/ld+json; charset=utf-8"
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/Utilisateur |
      | @type    | Utilisateur           |
      | @id      | /utilisateurs/renfort |
    Et le noeud JSON "roles[0]" devrait contenir "ROLE_USER"
    Et le noeud JSON "roles[1]" devrait contenir "ROLE_PLANIFICATEUR"
    Et le noeud JSON "roles[2]" devrait contenir "ROLE_RENFORT"

  @modifyData
  Scénario: En tant qu'utilisateur avec ROLE_RENFORT je peux donner ROLE_BENEFICIAIRE à un utilisateur
    Etant donné que j'envoie un token d'authentification pour renfort
    Et j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Et j'envoie une requête "PATCH" sur "/utilisateurs/nouveau-beneficiaire" avec le contenu :
  """
    {
    "roles":["ROLE_BENEFICIAIRE"]
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et le noeud JSON "roles[0]" devrait contenir "ROLE_USER"
    Et le noeud JSON "roles[1]" devrait contenir "ROLE_BENEFICIAIRE"

  @modifyData
  Scénario: Tout utilisateur peut modifier ses mail et téléphone perso
    Etant donné que  j'envoie un token d'authentification pour beneficiaire
    Et j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Quand j'envoie une requête "PATCH" sur "/utilisateurs/beneficiaire" avec le contenu :
  """
    {
    "emailPerso":"titi@tutu.com"
    }
    """
    Alors la réponse devrait être du JSON
    Et le code de status de la réponse devrait être 200
    Et les noeuds JSON devraient contenir:
      | @context   | /contexts/Utilisateur      |
      | @type      | Utilisateur                |
      | @id        | /utilisateurs/beneficiaire |
      | emailPerso | titi@tutu.com              |

  Scénario: En tant que simple utilisateur je ne peux pas modifier tel et mail persos de quelqu'un d'autre
    Etant donné que  j'envoie un token d'authentification pour beneficiaire
    Et j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Quand j'envoie une requête "PATCH" sur "/utilisateurs/beneficiaire2" avec le contenu :
  """
    {
    "emailPerso":"titi@tutu.com"
    }
    """
    Alors le code de status de la réponse devrait être 403

  @modifyData
  Scénario: En tant qu'admin je peux donner ROLE_RENFORT à un intervenant
    Etant donné que  j'envoie un token d'authentification pour admin
    Et j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Quand j'envoie une requête "PATCH" sur "/utilisateurs/intervenant" avec le contenu :
  """
    {
    "roles":["ROLE_INTERVENANT", "ROLE_RENFORT"],
    "services": ["/services/1"]
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et le noeud JSON "roles[0]" devrait contenir "ROLE_USER"
    Et le noeud JSON "roles[1]" devrait contenir "ROLE_INTERVENANT"
    Et le noeud JSON "roles[2]" devrait contenir "ROLE_PLANIFICATEUR"
    Et le noeud JSON "roles[3]" devrait contenir "ROLE_RENFORT"

  Scénario: Je peux à la fois filtrer par rôle et paginer / trier par nom
    Etant donné que j'envoie un token d'authentification pour admin
    Et qu'il y a 10 utilisateurs avec le rôle ROLE_GESTIONNAIRE
    Et qu'il y a 10 utilisateurs avec le rôle ROLE_RENFORT
    Et qu'il y a 200 utilisateurs avec le rôle ROLE_USER
    Quand j'envoie une requête "GET" sur '/roles/ROLE_PLANIFICATEUR/utilisateurs?page=1&itemsPerPage=7&order[nom]=desc'
    Alors le code de status de la réponse devrait être 200
    Et les noeuds JSON devraient contenir:
      | @context         | /contexts/Utilisateur |
      | hydra:totalItems | 20                    |
    Et le noeud JSON "hydra:member[6]" devrait exister
    Et le noeud JSON "hydra:member[7]" ne devrait pas exister

  @modifyData
  Scénario: Un utilisateur membre d'une commission en cours a le rôle ROLE_MEMBRE_COMMISSION
    Etant donné que j'envoie un token d'authentification pour admin
    Et qu'il existe un utilisateur "membre_commission" membre d'une commission en cours
    Quand j'envoie une requête "GET" sur "/utilisateurs/membre_commission"
    Alors la réponse devrait être du JSON
    Et le code de status de la réponse devrait être 200
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/Utilisateur           |
      | @type    | Utilisateur                     |
      | @id      | /utilisateurs/membre_commission |
    Et le noeud JSON "roles" devrait exister
    Et le noeud JSON "roles[1]" devrait exister
    Et le noeud JSON "roles[1]" devrait contenir "ROLE_MEMBRE_COMMISSION"



