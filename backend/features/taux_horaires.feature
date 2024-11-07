Feature: Taux horaires

  As an authenticated user
  I can use CRUD operations on /types_evenements/{typeId}/taux according to my role(s)

  Scenario: I need to have "planificateur" rights
    Given I send an authentication token for beneficiaire
    When I send a "GET" request to "/types_evenements/1/taux/1"
    Then the response status code should be 403

  Scenario: I can fetch one taux
    Given I send an authentication token for intervenant
    When I send a "GET" request to "/types_evenements/1/taux/1"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context | /contexts/TauxHoraire      |
      | @type    | TauxHoraire                |
      | @id      | /types_evenements/1/taux/1 |

  Scenario: I can't access taux through the wrong type evenement
    Given I send an authentication token for admin
    When I send a "GET" request to "/types_evenements/2/taux/1"
    Then the response status code should be 422

  Scenario: As admin I can create new taux
    Given I send an authentication token for admin
    And I add "Content-Type" header equal to "application/ld+json"
    When I send a "POST" request to "/types_evenements/2/taux" with body:
    """
    {
    "montant" : "12.12",
    "debut" : "2020-01-01",
    "fin" : "2020-12-31"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context | /contexts/TauxHoraire      |
      | @type    | TauxHoraire                |
      | @id      | /types_evenements/2/taux/2 |

  @modifyData
  Scenario: As admin I can modify a taux
    Given I send an authentication token for admin
    And I add "Content-Type" header equal to "application/merge-patch+json"
    When I send a "PATCH" request to "/types_evenements/1/taux/1" with body:
    """
    {
    "montant":"43.21"
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context | /contexts/TauxHoraire      |
      | @type    | TauxHoraire                |
      | @id      | /types_evenements/1/taux/1 |
      | montant  | 43.21                      |

  @modifyData
  Scenario: As admin I can delete a taux
    Given I send an authentication token for admin
    When I send a "DELETE" request to "/types_evenements/1/taux/1"
    Then the response status code should be 204

  Scenario: The "montant" field should be a valid decimal(5,2)
    Given I send an authentication token for admin
    And I add "Content-Type" header equal to "application/ld+json"
    When I send a "POST" request to "/types_evenements/2/taux" with body:
    """
    {
    "montant" : "12,12",
    "debut" : "2020-01-01",
    "fin" : "2020-12-31"
    }
    """
    Then the response status code should be 422

  Scenario: I can't create a new taux if there's already an active one
    Given I send an authentication token for admin
    And I add "Content-Type" header equal to "application/ld+json"
    When I send a "POST" request to "/types_evenements/1/taux" with body:
    """
    {
    "montant" : "12.12",
    "debut" : "2020-01-01"
    }
    """
    Then the response status code should be 422