Feature: Beneficiaires-Profils

  As an authenticated user
  I can use CRUD operations on /utilisateurs/{uid}/profils according to my role(s)

  Scenario: I need to be authenticated
    Given I send no authentication token
    When I send a "GET" request to "/utilisateurs/beneficiaire/profils/1"
    Then the response status code should be 401

  Scenario: As an authorized user i can fetch one profil
    Given I send an authentication token for admin
    When I send a "GET" request to "/utilisateurs/beneficiaire/profils/1"
    Then the response status code should be 200
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the response should be in JSON
    And the JSON nodes should contain:
      | @context | /contexts/BeneficiaireProfil         |
      | @type    | BeneficiaireProfil                   |
      | @id      | /utilisateurs/beneficiaire/profils/1 |

  Scenario: As a non-authorized user i can't fetch any
    Given I send an authentication token for beneficiaire2
    When I send a "GET" request to "/utilisateurs/beneficiaire/profils/1"
    Then the response status code should be 403

  @modifyData
  Scenario: As an authorized user I can add a "beneficiaire"
    Given I send an authentication token for gestionnaire
    And  I add "Content-Type" header equal to "application/ld+json"
    When I send a "POST" request to '/utilisateurs/nouveau-beneficiaire/profils' with body:
    """
    {
    "profil":"/profils/3",
    "debut":"2020-01-01",
    "gestionnaire":"/utilisateurs/gestionnaire"
    }
    """
    Then the response status code should be 201
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the response should be in JSON
    And the JSON nodes should contain:
      | @context     | /contexts/BeneficiaireProfil                 |
      | @type        | BeneficiaireProfil                           |
      | @id          | /utilisateurs/nouveau-beneficiaire/profils/3 |
      | profil       | /profils/3                                   |
      | gestionnaire | /utilisateurs/gestionnaire                   |

  @modifyData
  Scenario: As an authorized user I can add a "beneficiaire" without specifying a "profil"
    Given I send an authentication token for gestionnaire
    And  I add "Content-Type" header equal to "application/ld+json"
    When I send a "POST" request to '/utilisateurs/nouveau-beneficiaire/profils' with body:
    """
    {
    "debut":"2020-01-01",
    "gestionnaire":"/utilisateurs/gestionnaire"
    }
    """
    Then the response status code should be 201
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the response should be in JSON
    And the JSON nodes should contain:
      | @context     | /contexts/BeneficiaireProfil |
      | @type        | BeneficiaireProfil           |
      | profil       | /profils/-1                  |
      | gestionnaire | /utilisateurs/gestionnaire   |

  @modifyData
  Scenario: As an authorized user i can modify existing beneficiaires
    Given I send an authentication token for gestionnaire
    And  I add "Content-Type" header equal to "application/merge-patch+json"
    When I send a "PATCH" request to '/utilisateurs/beneficiaire/profils/1' with body:
    """
    {
    "fin":"2023-01-01"
    }
    """
    Then the response status code should be 200
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the response should be in JSON
    And the JSON nodes should contain:
      | @context | /contexts/BeneficiaireProfil         |
      | @type    | BeneficiaireProfil                   |
      | @id      | /utilisateurs/beneficiaire/profils/1 |
      | fin      | 2023-01-01                           |

  @modifyData
  Scenario: As an authorized user i can specify Typologies
    Given I send an authentication token for gestionnaire
    And  I add "Content-Type" header equal to "application/merge-patch+json"
    When I send a "PATCH" request to '/utilisateurs/beneficiaire2/profils/2' with body:
    """
    {
    "typologies": ["/typologies/1"]
    }
    """
    Then the response status code should be 200
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the response should be in JSON
    And the JSON nodes should contain:
      | @context | /contexts/BeneficiaireProfil          |
      | @type    | BeneficiaireProfil                    |
      | @id      | /utilisateurs/beneficiaire2/profils/2 |
    And the JSON node typologies should exist
    And the JSON node "typologies[0]" should contain "/typologies/1"

  Scenario: A beneficiaire cannot be his own gestionnaire
    Given I send an authentication token for gestionnaire
    And  I add "Content-Type" header equal to "application/merge-patch+json"
    When I send a "PATCH" request to '/utilisateurs/beneficiaire/profils/1' with body:
    """
    {
    "gestionnaire":"/utilisateurs/beneficiaire"
    }
    """
    Then the response status code should be 422

  @modifyData
  Scenario: As an authorized user i can delete existing beneficiaires
    Given I send an authentication token for gestionnaire
    When I send a "DELETE" request to '/utilisateurs/beneficiaire2/profils/2'
    Then the response status code should be 204

  Scenario: I can't delete a beneficiaire that has associated events
    Given I send an authentication token for gestionnaire
    When I send a "DELETE" request to '/utilisateurs/beneficiaire/profils/1'
    Then the response status code should be 422