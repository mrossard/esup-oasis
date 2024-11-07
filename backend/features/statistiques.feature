Feature: Statistiques

  As an authenticated user
  I have access to relevant stats according to my role(s)

  @modifyData
  Scenario: As an authenticated user I can get unfiltered stats for my dashboard
    Given I send an authentication token for gestionnaire
    And today is "2040-02-01"
    And there is an event for user gestionnaire2 tomorrow
    When I send a "GET" request to "/statistiques"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | @context                        | /contexts/TableauDeBord |
      | @id                             | /statistiques           |
      | evenementsJour                  | 0                       |
      | evolutionJour                   | 0                       |
      | evenementsSemaine               | 1                       |
      | evolutionSemaine                | 1                       |
      | evenementsMois                  | 1                       |
      | evolutionMois                   | 1                       |
      | evenementsNonAffectesJour       | 0                       |
      | evenementsNonAffectesSemaine    | 1                       |
      | evenementsNonAffectesMois       | 1                       |
      | totalEvenementsNonAffectes      | 1                       |
      | evenementsEnAttenteDeValidation | 0                       |
      | nbBeneficiairesIncomplets       | 0                       |

  @modifyData
  Scenario: As an authenticated user I can get personal stats for my dashboard
    Given I send an authentication token for gestionnaire
    And there is an event for user gestionnaire2 tomorrow
    When I send a "GET" request to "/statistiques?utilisateur=/utilisateurs/gestionnaire"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | @context                        | /contexts/TableauDeBord |
      | @id                             | /statistiques           |
      | evenementsJour                  | 0                       |
      | evolutionJour                   | 0                       |
      | evenementsSemaine               | 0                       |
      | evolutionSemaine                | 0                       |
      | evenementsMois                  | 0                       |
      | evolutionMois                   | 0                       |
      | evenementsNonAffectesJour       | 0                       |
      | evenementsNonAffectesSemaine    | 0                       |
      | evenementsNonAffectesMois       | 0                       |
      | totalEvenementsNonAffectes      | 0                       |
      | evenementsEnAttenteDeValidation | 0                       |
      | nbBeneficiairesIncomplets       | 0                       |

  @modifyData
  Scenario: With ROLE_INTERVENANT I can only see limited individual stats
    Given I send an authentication token for intervenant
    And today is "2040-02-01"
    And there is an event for user gestionnaire tomorrow with intervenant
    And there is an event for user gestionnaire tomorrow with intervenant2
    When I send a "GET" request to "/statistiques"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | @context          | /contexts/TableauDeBord |
      | @id               | /statistiques           |
      | evenementsJour    | 0                       |
      | evenementsSemaine | 1                       |
      | evenementsMois    | 1                       |
    And the JSON node "evolutionJour" should not exist
    And the JSON node "evolutionSemaine" should not exist
    And the JSON node "evolutionMois" should not exist

  Scenario: With ROLE_BENEFICIAIRE I am not authorized
    Given I send an authentication token for beneficiaire
    When I send a "GET" request to "/statistiques"
    Then the response status code should be 403