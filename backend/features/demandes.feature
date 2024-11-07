# language: fr
Fonctionnalité: Demandes
  En tant qu'étudiant je peux effectuer des demandes auprès de PHASE en fonction de mon profil
  En tant qu'admin/gestionnaire je peux gérer les demandes

  Scénario: Je peux obtenir la liste des types de demandes proposés
    Etant donné que j'envoie un token d'authentification pour demandeur
    Quand j'envoie une requête "GET" sur "/types_demandes"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/TypeDemande |
      | @type    | hydra:Collection      |
      | @id      | /types_demandes       |

  @modifyData
  Scénario: En tant qu'admin je peux créer un type de demande
    Etant donné que j'envoie un token d'authentification pour admin
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "POST" sur "/types_demandes" avec le contenu :
    """
    {
      "libelle" : "nouveau type"
    }
    """
    Alors le code de status de la réponse devrait être 201
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/TypeDemande |
      | @type    | TypeDemande           |
      | libelle  | nouveau type          |

  @modifyData
  Scénario: En tant qu'admin je peux modifier un type de demande
    Etant donné que j'envoie un token d'authentification pour admin
    Et que j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Quand j'envoie une requête "PATCH" sur "/types_demandes/1" avec le contenu :
    """
    {
      "libelle" : "nouveau libelle"
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/TypeDemande |
      | @type    | TypeDemande           |
      | @id      | /types_demandes/1     |
      | libelle  | nouveau libelle       |


  Scénario: Pour chaque type de demande, on peut obtenir la liste des campagnes organisées
    Etant donné que j'envoie un token d'authentification pour demandeur
    Quand j'envoie une requête "GET" sur "/types_demandes/1/campagnes"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/CampagneDemande   |
      | @type    | hydra:Collection            |
      | @id      | /types_demandes/1/campagnes |

  @modifyData
  Scénario: En tant qu'admin je peux créer une nouvelle campagne
    Etant donné que j'envoie un token d'authentification pour admin
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "POST" sur "/types_demandes/1/campagnes" avec le contenu :
    """
    {
      "libelle" : "nouvelle campagne",
      "debut" : "2040-01-01",
      "fin" : "2041-01-01",
      "dateCommission" : "2041-02-01",
      "commission":"/commissions/1"
    }
    """
    Alors le code de status de la réponse devrait être 201
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/CampagneDemande     |
      | @type    | CampagneDemande               |
      | @id      | /types_demandes/1/campagnes/2 |

  @modifyData
  Scénario: En tant qu'admin je peux créer une campagne sans commission
    Etant donné que j'envoie un token d'authentification pour admin
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "POST" sur "/types_demandes/1/campagnes" avec le contenu :
    """
    {
      "libelle" : "nouvelle campagne",
      "debut" : "2040-01-01",
      "fin" : "2041-01-01"
    }
    """
    Alors le code de status de la réponse devrait être 201
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/CampagneDemande     |
      | @type    | CampagneDemande               |
      | @id      | /types_demandes/1/campagnes/2 |

  @modifyData
  Scénario: En tant qu'admin je peux modifier une campagne existante
    Etant donné que j'envoie un token d'authentification pour admin
    Et que j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Quand j'envoie une requête "PATCH" sur "/types_demandes/1/campagnes/1" avec le contenu :
    """
    {
      "libelle" : "nouveau libelle"
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/CampagneDemande     |
      | @type    | CampagneDemande               |
      | @id      | /types_demandes/1/campagnes/1 |
      | libelle  | nouveau libelle               |

  @modifyData
  Scénario: En tant que simple demandeur je ne vois que mes propres demandes
    Etant donné que j'envoie un token d'authentification pour demandeur
    Et que l'utilisateur demandeur a une demande "en cours" pour le type 1
    Et que l'utilisateur demandeur a une demande "en cours" pour le type 2
    Et que l'utilisateur demandeur2 a une demande "en cours" pour le type 1
    Et que l'utilisateur gestionnaire a une demande "en cours" pour le type 1
    Quand j'envoie une requête "GET" sur "/demandes"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context         | /contexts/Demande |
      | @type            | hydra:Collection  |
      | @id              | /demandes         |
      | hydra:totalItems | 2                 |

  @modifyData
  Scénario: Je peux reprendre une demande "en cours" pour un certain type
    Etant donné que j'envoie un token d'authentification pour demandeur
    Et que l'utilisateur demandeur a une demande "en cours" pour le type 1
    Quand j'envoie une requête "GET" sur "/demandes?demandeur=/utilisateurs/demandeur&typeDemande=/types_demandes/1"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context         | /contexts/Demande |
      | @type            | hydra:Collection  |
      | @id              | /demandes         |
      | hydra:totalItems | 1                 |


  @modifyData
  Scénario: Si une réponse existante ajoute une question, elle remonte
    Etant donné que j'envoie un token d'authentification pour demandeur
    Et que l'utilisateur demandeur a une demande "en cours" pour le type 1
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Et que j'envoie une requête "PUT" sur "/demandes/1/questions/3/reponse" avec le contenu :
    """
    {
      "optionsChoisies" : ["/questions/3/options/3"]
    }
    """
    Alors le code de status de la réponse devrait être 201
    Et que j'envoie un token d'authentification pour demandeur
    Quand j'envoie une requête "GET" sur "/demandes/1"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/Demande |
      | @type    | Demande           |
      | @id      | /demandes/1       |
    Et le noeud JSON "etapes" devrait exister
    Et le noeud JSON "etapes[1]" devrait exister
    Et le noeud JSON "etapes[1].questions[0]" devrait exister
    Et le noeud JSON "etapes[1].questions[0].libelle" devrait contenir "Avez-vous un numéro machin bidule du ministère?"
    Et le noeud JSON "etapes[1].questions[0].reponse" devrait exister
    Et le noeud JSON "etapes[1].questions[0].reponse.optionsReponses[0].libelle" devrait contenir "oui"
    Et le noeud JSON "etapes[1].questions[1]" devrait exister
    Et le noeud JSON "etapes[1].questions[1].libelle" devrait contenir "Votre numéro machin bidule du ministère :"


  @modifyData
  Scénario: En tant que gestionnaire je peux filtrer les demandes en fonction du libellé de leur type
    Etant donné que j'envoie un token d'authentification pour gestionnaire
    Et que l'utilisateur demandeur a une demande "en cours" pour le type 1
    Quand j'envoie une requête "GET" sur "/demandes?typeDemande.nom=SPORT"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context         | /contexts/Demande |
      | @type            | hydra:Collection  |
      | @id              | /demandes         |
      | hydra:totalItems | 1                 |

  @modifyData
  Scénario: En tant que gestionnaire je peux filtrer les demandes en fonction du nom du demandeur
    Etant donné que j'envoie un token d'authentification pour gestionnaire
    Et que l'utilisateur demandeur a une demande "en cours" pour le type 1
    Et que l'utilisateur gestionnaire a une demande "en cours" pour le type 1
    Quand j'envoie une requête "GET" sur "/demandes?demandeur.nom=DeM"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context         | /contexts/Demande |
      | @type            | hydra:Collection  |
      | @id              | /demandes         |
      | hydra:totalItems | 1                 |

  @modifyData
  Scénario: En tant que demandeur je peux démarrer une nouvelle demande
    Etant donné que j'envoie un token d'authentification pour demandeur
    Et qu'il y a une campagne en cours pour le type de demande 1
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "POST" sur "/demandes" avec le contenu :
  """
    {
    "typeDemande":"/types_demandes/1"
    }
  """
    Alors le code de status de la réponse devrait être 201
    Et la réponse devrait être du JSON
    Et le noeud JSON "demandeur.uid" devrait contenir "demandeur"
    Et le noeud JSON "etat" devrait exister
    Et le noeud JSON "etapes" devrait exister
    Et le noeud JSON "etapes[0]" devrait exister
    Et le noeud JSON "etapes[0].etape" devrait contenir "/etapes_demandes/1"
    Et le noeud JSON "etapes[0].questions" devrait exister
    Et le noeud JSON "etapes[0].questions[0]" devrait exister
    Et le noeud JSON "etapes[0].questions[0].id" devrait contenir "1"
    Et le noeud JSON "etapes[0].questions[0].question" devrait contenir "/questions/1"
    Et le noeud JSON "etapes[0].questions[1].id" devrait contenir "2"

  @modifyData
  Scénario: Si j'ai déjà répondu à une des questions de ma nouvelle demande je récupère la réponse existante
    Etant donné que j'envoie un token d'authentification pour demandeur
    Et qu'il y a une campagne en cours pour le type de demande 1
    Et que demandeur a une réponse à la question 1 pour le type de demande 2
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "POST" sur "/demandes" avec le contenu :
  """
    {
    "typeDemande":"/types_demandes/1"
    }
  """
    Alors le code de status de la réponse devrait être 201
    Et le noeud JSON "etapes[0].questions[0].id" devrait contenir "1"
    Et le noeud JSON "etapes[0].questions[0].reponse" devrait exister
    Et le noeud JSON "etapes[0].questions[0].reponse.commentaire" devrait exister
    Et le noeud JSON "etapes[0].questions[0].reponse.commentaire" devrait contenir "réponse de demandeur"

  @modifyData
  Scénario: Un gestionnaire peut démarrer une demande pour quelqu'un d'autre
    Etant donné que j'envoie un token d'authentification pour gestionnaire
    Et qu'il y a une campagne en cours pour le type de demande 1
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "POST" sur "/demandes" avec le contenu :
  """
    {
    "typeDemande":"/types_demandes/1",
    "demandeur": "/utilisateurs/demandeur"
    }
  """
    Alors le code de status de la réponse devrait être 201
    Et la réponse devrait être du JSON
    Et le noeud JSON "demandeur.uid" devrait contenir "demandeur"
    Et le noeud JSON "etat" devrait exister

  @modifyData
  Scénario: Un simple utilisateur ne peut pas démarrer une demande pour quelqu'un d'autre
    Etant donné que j'envoie un token d'authentification pour demandeur
    Et qu'il y a une campagne en cours pour le type de demande 1
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "POST" sur "/demandes" avec le contenu :
  """
    {
    "typeDemande":"/types_demandes/1",
    "demandeur": "/utilisateurs/intervenant"
    }
  """
    Alors le code de status de la réponse devrait être 403

  @modifyData
  Scénario: Un gestionnaire peut modifier l'état d'une demande
    Etant donné que l'utilisateur demandeur a une demande "réceptionnée" pour le type 1
    Et que j'envoie un token d'authentification pour gestionnaire
    Et que j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Quand j'envoie une requête "PATCH" sur "/demandes/1" avec le contenu :
  """
  {
    "etat":"/etats_demandes/3"
  }
  """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et le noeud JSON "etat" devrait contenir "/etats_demandes/3"

  @modifyData
  Scénario: Un demandeur peut passer l'état de sa demande à "réceptionnée"
    Etant donné que l'utilisateur demandeur a une demande "en cours" pour le type 5
    Et que j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Quand j'envoie une requête "PATCH" sur "/demandes/1" avec le contenu :
  """
  {
    "etat":"/etats_demandes/2"
  }
  """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et le noeud JSON "etat" devrait contenir "/etats_demandes/2"

  @modifyData
  Scénario: Répondre à une question de type "submit" passe la demande à "réceptionnée"
    Etant donné que l'utilisateur demandeur a une demande "en cours" pour le type 1
    Et que j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Et que j'envoie une requête "PUT" sur "/demandes/1/questions/1/reponse" avec le contenu :
    """
    {
      "optionsChoisies" : ["/questions/1/options/1"]
    }
    """
    Et que le code de status de la réponse devrait être 201
    Et que j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Et que j'envoie une requête "PUT" sur "/demandes/1/questions/3/reponse" avec le contenu :
    """
    {
      "optionsChoisies" : ["/questions/3/options/3"]
    }
    """
    Et que le code de status de la réponse devrait être 201
    Et que j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Et que j'envoie une requête "PUT" sur "/demandes/1/questions/4/reponse" avec le contenu :
    """
    {
      "commentaire": "123456"
    }
    """
    Et que le code de status de la réponse devrait être 201
    Et que j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Et que j'envoie une requête "PUT" sur "/demandes/1/questions/7/reponse" avec le contenu :
    """
    {
      "commentaire" : "Validé le 01/01/2024 à 12:30"
    }
    """
    Et que le code de status de la réponse devrait être 201
    Et que j'envoie un token d'authentification pour demandeur
    Quand j'envoie une requête "GET" sur "/demandes/1"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/Demande |
      | @type    | Demande           |
      | @id      | /demandes/1       |
      | etat     | /etats_demandes/2 |
    Et le noeud JSON "complete" devrait être vrai

  @modifyData
  Scénario: Répondre à une question de type "submit" échoue si le questionnaire est incomplet
    Etant donné que l'utilisateur demandeur a une demande "en cours" pour le type 1
    Et que j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Et que j'envoie une requête "PUT" sur "/demandes/1/questions/1/reponse" avec le contenu :
    """
    {
      "optionsChoisies" : ["/questions/1/options/1"]
    }
    """
    Et que j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "PUT" sur "/demandes/1/questions/7/reponse" avec le contenu :
    """
    {
      "commentaire" : "Validé le 01/01/2024 à 12:30"
    }
    """
    Alors le code de status de la réponse devrait être 422

  @modifyData
  Scénario: Un demandeur peut répondre aux questions d'une demande "en cours"
    Etant donné que l'utilisateur demandeur a une demande "en cours" pour le type 1
    Et que j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "PUT" sur "/demandes/1/questions/3/reponse" avec le contenu :
    """
    {
      "optionsChoisies" : ["/questions/3/options/3"]
    }
    """
    Alors le code de status de la réponse devrait être 201
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context              | /contexts/Reponse               |
      | @type                 | Reponse                         |
      | @id                   | /demandes/1/questions/3/reponse |
      | optionsChoisies[0].id | 3                               |

  @modifyData
  Scénario: Un demandeur peut modifier une réponse existante
    Etant donné que l'utilisateur demandeur a une demande "en cours" pour le type 1
    Et que j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Et que j'envoie une requête "PUT" sur "/demandes/1/questions/3/reponse" avec le contenu :
    """
    {
      "optionsChoisies" : ["/questions/3/options/3"]
    }
    """
    Et que j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "PUT" sur "/demandes/1/questions/3/reponse" avec le contenu :
    """
    {
      "optionsChoisies" : ["/questions/3/options/4"]
    }
    """
    Alors le code de status de la réponse devrait être 201
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context              | /contexts/Reponse               |
      | @type                 | Reponse                         |
      | @id                   | /demandes/1/questions/3/reponse |
      | optionsChoisies[0].id | 4                               |

  @modifyData
  Scénario: Un demandeur ne peut pas donner une réponse hors des options proposées
    Etant donné que l'utilisateur demandeur a une demande "en cours" pour le type 1
    Et que j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "PUT" sur "/demandes/1/questions/1/reponse" avec le contenu :
    """
    {
      "optionsChoisies" : ["/questions/2/options/4"]
    }
    """
    Alors le code de status de la réponse devrait être 422

  @modifyData
  Scénario: Un demandeur ne peut pas répondre un simple commentaire à la place d'une option
    Etant donné que l'utilisateur demandeur a une demande "en cours" pour le type 1
    Et que j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "PUT" sur "/demandes/1/questions/1/reponse" avec le contenu :
    """
    {
      "commentaire": "blabliblu"
    }
    """
    Alors le code de status de la réponse devrait être 422

  @modifyData
  Scénario: Certaines questions utilisent une liste d'options basées sur une table de référence
    Etant donné qu'il existe 50 clubs sportifs professionnels
    Et que l'utilisateur demandeur a une demande "en cours" pour le type 1
    Et que j'envoie un token d'authentification pour demandeur
    Quand j'envoie une requête "GET" sur "/questions/5"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/Question |
      | @type    | Question           |
      | @id      | /questions/5       |
    Et le noeud JSON "optionsReponses" devrait exister
    Et le noeud JSON "optionsReponses[0]" devrait exister
    Et le noeud JSON "optionsReponses[49]" devrait exister

  @modifyData
  Scénario: Je peux répondre quand une table de référence est utilisée
    Etant donné qu'il existe 50 clubs sportifs professionnels
    Et que l'utilisateur demandeur a une demande "en cours" pour le type 1
    Et que j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "PUT" sur "/demandes/1/questions/5/reponse" avec le contenu :
    """
    {
      "optionsChoisies": ["/questions/5/options/33"]
    }
    """
    Alors le code de status de la réponse devrait être 201
    Et la réponse devrait être du JSON
    Et le noeud JSON "optionsChoisies[0]" devrait exister
    Et le noeud JSON "optionsChoisies[0].id" devrait contenir "33"
    Etant donné que j'envoie un token d'authentification pour demandeur
    Quand j'envoie une requête "GET" sur "/demandes/1"
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON
    Et le noeud JSON "etapes" devrait exister
    Et le noeud JSON "etapes[1].questions[1]" devrait exister
    Et le noeud JSON "etapes[1].questions[1].reponse.optionsReponses" devrait exister
    Et le noeud JSON "etapes[1].questions[1].reponse.optionsReponses[0].id" devrait contenir "33"

  @modifyData
  Scénario: Un demandeur peut répondre à une demande de pièce justificative
    Etant donné que l'utilisateur demandeur a une demande "en cours" pour le type 1
    Et que j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "multipart/form-data"
    Et que j'envoie une requête "POST" sur "/telechargements" avec les paramètres :
      | key  | value             |
      | file | @piece_justif.txt |
    Et que le code de status de la réponse devrait être 201
    Et que les noeuds JSON devraient contenir:
      | @context   | /contexts/Telechargement |
      | @id        | /telechargements/1       |
      | urlContenu | /fichiers/1              |
    Et que j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Quand j'envoie une requête "PUT" sur "/demandes/1/questions/8/reponse" avec le contenu :
    """
    {
      "piecesJustificatives": ["/telechargements/1"]
    }
    """
    Alors le code de status de la réponse devrait être 201
    Et les noeuds JSON devraient contenir:
      | @context | /contexts/Reponse               |
      | @id      | /demandes/1/questions/8/reponse |
    Et le noeud JSON "piecesJustificatives[0]" devrait contenir "/telechargements/1"

  @modifyData
  Scénario: Répondre "oui" à la question "Souhaitez-vous un accompagnement"
  fait apparaitre les questions suivantes
    Etant donné que l'utilisateur demandeur a une demande "en cours" pour le type 3
    Quand j'envoie un token d'authentification pour demandeur
    Et que j'envoie une requête "GET" sur "/demandes/1"
    Alors le code de status de la réponse devrait être 200
    Et le noeud JSON "etapes" devrait exister
    Et le noeud JSON "etapes" devrait avoir 3 éléments
    Et le noeud JSON "etapes[1].questions" devrait exister
    Et le noeud JSON "etapes[1].questions" devrait avoir 1 élément
    Et le noeud JSON "etapes[1].questions[0].id" devrait contenir "-1"
    Quand j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Et que j'envoie une requête "PUT" sur "/demandes/1/questions/-1/reponse" avec le contenu :
    """
    {
      "optionsChoisies" : ["/questions/-1/options/-1"]
    }
    """
    Alors le code de status de la réponse devrait être 201
    Quand j'envoie un token d'authentification pour demandeur
    Et que j'envoie une requête "GET" sur "/demandes/1"
    Alors le noeud JSON "etapes[1].questions" devrait avoir 2 éléments
    Et le noeud JSON "etapes[1].questions[0].id" devrait contenir "-1"
    Et le noeud JSON "etapes[1].questions[1].id" ne devrait pas contenir "-1"
    Quand j'envoie un token d'authentification pour demandeur
    Et que j'ajoute l'entête "Content-Type" égale à "application/ld+json"
    Et que j'envoie une requête "PUT" sur "/demandes/1/questions/-1/reponse" avec le contenu :
    """
    {
      "optionsChoisies" : ["/questions/-1/options/-2"]
    }
    """
    Alors le code de status de la réponse devrait être 201
    Quand j'envoie un token d'authentification pour demandeur
    Et que j'envoie une requête "GET" sur "/demandes/1"
    Alors le noeud JSON "etapes[1].questions" devrait avoir 1 élément
    Et le noeud JSON "etapes[1].questions[0].id" devrait contenir "-1"

  @modifyData
  Scénario: Passer conforme une demande d'un type lié à un seul profil, sans commission et qui ne propose
  pas le profil sans accompagnement la fait passer à "en attente d'accompagnement"
    Etant donné que l'utilisateur demandeur a une demande réceptionnée pour le type 2 sans commission
    Quand j'envoie un token d'authentification pour admin
    Et que j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Et que j'envoie une requête "PATCH" sur "/demandes/1" avec le contenu :
    """
    {
      "etat" : "/etats_demandes/3"
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et le noeud JSON "etat" devrait exister
    Et le noeud JSON "etat" devrait contenir "/etats_demandes/10"

  @modifyData
  Scénario: Passer une demande sans accompagnement à "profil validé", sans commission, la fait passer à l'état "validé"
    Etant donné que l'utilisateur demandeur a une demande réceptionnée pour le type 2 sans commission
    Et que le type de demande 2 propose le profil sans accompagnement
    Et que l'utilisateur demandeur a répondu non à la question souhait d'accompagnement pour la demande 1
    Quand j'envoie un token d'authentification pour admin
    Et que j'ajoute l'entête "Content-Type" égale à "application/merge-patch+json"
    Et que j'envoie une requête "PATCH" sur "/demandes/1" avec le contenu :
    """
    {
      "etat" : "/etats_demandes/3"
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et le noeud JSON "etat" devrait exister
    Et le noeud JSON "etat" devrait contenir "/etats_demandes/4"

  @modifyData
  Scénario: Un membre de commission peut voir les demandes de ses commissions
    Etant donné que l'utilisateur demandeur a une demande réceptionnée pour le type 4 avec commission et un membre membrecommission
    Et que j'envoie un token d'authentification pour membrecommission
    Quand j'envoie une requête "GET" sur "/demandes/1"
    Alors le code de status de la réponse devrait être 200


