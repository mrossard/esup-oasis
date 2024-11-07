Feature: Periodes

  As an admin I can use CRUD operations on /periodes

  Scenario: I need to have ROLE_PLANIFICATEUR or ROLE_INTERVENANT
    Given I send an authentication token for beneficiaire
    When I send a "GET" request to "/periodes"
    Then the response status code should be 403

  Scenario: I can list all Periodes
    Given I send an authentication token for admin
    When I send a "GET" request to "/periodes"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context | /contexts/PeriodeRH |
      | @type    | hydra:Collection    |
      | @id      | /periodes           |

  @modifyData
  Scenario: I can create a new Periode
    Given I send an authentication token for admin
    And I add "Content-Type" header equal to "application/ld+json; charset=utf-8"
    When I send a "POST" request to "/periodes" with body:
    """
    {
    "debut":"2012-01-01",
    "butoir":"2012-01-29",
    "fin":"2012-01-31"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context | /contexts/PeriodeRH |
      | @type    | PeriodeRH           |
      | @id      | /periodes/4         |

  Scenario: I can't have "butoir" after "fin"
    Given I send an authentication token for admin
    And I add "Content-Type" header equal to "application/ld+json; charset=utf-8"
    When I send a "POST" request to "/periodes" with body:
    """
    {
    "debut":"2021-01-01",
    "butoir":"2021-02-01",
    "fin":"2021-01-31"
    }
    """
    Then the response status code should be 422

  Scenario: I can't have "butoir" before "debut"
    Given I send an authentication token for admin
    And I add "Content-Type" header equal to "application/ld+json; charset=utf-8"
    When I send a "POST" request to "/periodes" with body:
    """
    {
    "debut":"2021-01-01",
    "butoir":"2020-12-31",
    "fin":"2021-01-31"
    }
    """
    Then the response status code should be 422

  Scenario: "butoir" and "fin" can be equal
    Given I send an authentication token for admin
    And I add "Content-Type" header equal to "application/ld+json; charset=utf-8"
    When I send a "POST" request to "/periodes" with body:
    """
    {
    "debut":"2013-01-01",
    "butoir":"2013-01-31",
    "fin":"2013-01-31"
    }
    """
    Then the response status code should be 201

  Scenario: I can find the latest locked Periode
    Given I send an authentication token for renfort
    When I send a GET request to "/periodes?dateButoir[before]=2040-06-27&order[debut]=desc&itemsPerPage=1&page=1"
    Then the response status code should be 200
    And the JSON node "hydra:search" should exist
    And the JSON node "hydra:member[0]" should exist
    And the JSON node "hydra:member[1]" should not exist

  Scenario: I can't have two Periodes at the same time
    Given I send an authentication token for admin
    And I add "Content-Type" header equal to "application/ld+json; charset=utf-8"
    When I send a "POST" request to "/periodes" with body:
    """
    {
    "debut":"2022-12-15",
    "butoir":"2023-01-10",
    "fin":"2023-01-15"
    }
    """
    Then the response status code should be 422
