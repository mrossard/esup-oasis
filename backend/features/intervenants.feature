Feature: Intervenants

  As a user with ROLE_INTERVENANT
  I have specific functionalities available

  Scenario: I can list my own "services faits"
    Given I send an authentication token for "intervenant2"
    When I send a GET request to "/intervenants/intervenant2/services_faits"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | @context | /contexts/ServicesFaits                   |
      | @id      | /intervenants/intervenant2/services_faits |
      | @type    | hydra:Collection                          |
    And the JSON node "hydra:member" should exist
    And the JSON node "hydra:totalItems" should contain "1"
    And the JSON node "hydra:member[0].@id" should contain "/intervenants/intervenant2/services_faits/1"

  Scenario: I cannot list "services faits" for other people
    Given I send an authentication token for "intervenant"
    When I send a GET request to "/intervenants/intervenant2/services_faits"
    Then the response status code should be 403