# language: fr
Fonctionnalité: : Application cliente

  Les applications clientes peuvent s'authentifier et obtenir certaines données spécifiques

  Scénario: Je peux m'authentifier en tant qu'application
    Etant donné qu'il existe une application d'identifiant "someapp" avec la clé d'api "somekey"
    Quand j'envoie une requête POST sur "/connect/apikey" avec le contenu :
    """
    {
    "appId":"someapp",
    "apiKey":"somekey"
    }
    """
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait contenir "ey"

  Scénario: Je peux obtenir la liste des événements d'un intervenant
    Etant donné qu'il existe une application d'identifiant "someapp" avec la clé d'api "somekey"
    Et que j'envoie un token d'authentification pour l'application "someapp"
    Quand j'envoie une requête "GET" sur '/evenements?intervenant=/utilisateurs/intervenant'
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON

  Scénario: Je peux obtenir la liste des événements d'un bénéficiaire
    Etant donné qu'il existe une application d'identifiant "someapp" avec la clé d'api "somekey"
    Et que j'envoie un token d'authentification pour l'application "someapp"
    Quand j'envoie une requête "GET" sur '/evenements?beneficiaires=/utilisateurs/beneficiaire'
    Alors le code de status de la réponse devrait être 200
    Et la réponse devrait être du JSON