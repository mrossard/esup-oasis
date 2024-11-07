Feature: Types Evenements

  As an authenticated user
  I can use CRUD operations on /types_evenements according to my role(s)

  Scenario: I need to be authenticated
    Given I send no authentication token
    When I send a "GET" request to "/types_evenements/1"
    Then the response status code should be 401

  Scenario: I can fetch one Type Evenement
    Given I send an authentication token for admin
    When I send a "Get" request to "/types_evenements/1"
    Then the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context  | /contexts/TypeEvenement    |
      | @type     | TypeEvenement              |
      | @id       | /types_evenements/1        |
      | tauxActif | /types_evenements/1/taux/1 |

  @modifyData
  Scenario: As "admin" I can patch a type
    Given I send an authentication token for admin
    When I add "Content-Type" header equal to "application/merge-patch+json"
    And I send a "PATCH" request to "/types_evenements/1" with body:
    """
{
      "libelle" : "nouveau",
      "forfait" : true
}
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context | /contexts/TypeEvenement |
      | @type    | TypeEvenement           |
      | @id      | /types_evenements/1     |

  @modifyData
  Scenario: As "admin" I can add a type
    Given I send an authentication token for admin
    When I add "Content-Type" header equal to "application/ld+json; charset=utf-8"
    And I send a "POST" request to "/types_evenements" with body:
    """
{
      "libelle": "qfsgh",
      "actif": true,
      "visibleParDefaut": true,
      "couleur": "brown",
      "forfait" : false
}
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context | /contexts/TypeEvenement |
      | @type    | TypeEvenement           |
