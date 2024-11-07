Feature: Profils

  As an admin
  I can use CRUD operations on /profils

  Scenario: I need to be authenticated
    Given I send no authentication token
    When I send a "GET" request to "/profils"
    Then the response status code should be 401

  Scenario: I can get all profils
    Given I send an authentication token for admin
    When I send a "GET" request to "/profils"
    Then the response status code should be 200
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context | /contexts/ProfilBeneficiaire |
      | @type    | hydra:collection             |
      | @id      | /profils                     |

  @modifyData
  Scenario: I can modify one profil
    Given I send an authentication token for admin
    And I add "Content-Type" header equal to "application/merge-patch+json"
    When I send a "PATCH" request to "/profils/1" with body:
    """
    {
    "libelle":"nouveau libelle"
    }
    """
    Then the response status code should be 200
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context | /contexts/ProfilBeneficiaire |
      | @type    | ProfilBeneficiaire           |
      | @id      | /profils/1                   |
      | libelle  | nouveau libelle              |

