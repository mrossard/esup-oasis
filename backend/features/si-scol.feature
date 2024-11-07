Feature: SI Scol

  As an authenticated user
  I can read what has been imported from the SI

  Scenario: I can get all composantes
    Given I send an authentication token for gestionnaire
    When I send a "GET" request to "/composantes"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context | /contexts/Composante |
      | @type    | hydra:Collection     |
      | @id      | /composantes         |

  Scenario: I can get all formation
    Given I send an authentication token for gestionnaire
    When I send a "GET" request to "/formations"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context | /contexts/Formation |
      | @type    | hydra:Collection    |
      | @id      | /formations         |