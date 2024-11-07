Feature: Services

  As an authenticated user
  I can use CRUD operations on /services according to my role

  Scenario: I need to be authenticated
    Given I send no authentication token
    When I send a "GET" request to "/services/1"
    Then the response status code should be 401

  Scenario: I can read /service as "admin"
    Given I send an authentication token for admin
    When I send a "GET" request to "/services"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON should be valid according to this schema:
    """
{
  "@context": "/contexts/Service",
  "@id": "/services",
  "@type": "hydra:Collection"
}
    """

  Scenario: I can read /service as "gestionnaire"
    Given I send an authentication token for gestionnaire
    When I send a "GET" request to "/services"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON should be valid according to this schema:
    """
{
  "@context": "/contexts/Service",
  "@id": "/services",
  "@type": "hydra:Collection"
}
    """

  @modifyData
  Scenario: I can write to /service as "admin"
    Given I send an authentication token for admin
    When I add "Content-Type" header equal to "application/ld+json"
    And I send a "POST" request to "/services" with body:
    """
{
      "libelle" : "nouveau",
      "actif" : true
}
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON should be valid according to this schema:
    """
{
  "@context": "/contexts/Service",
  "@type": "Service"
}
    """

  @modifyData
  Scenario: I can patch a service as "admin"
    Given I send an authentication token for admin
    When I add "Content-Type" header equal to "application/merge-patch+json"
    And I send a "PATCH" request to "/services/1" with body:
    """
{
      "libelle" : "nouveau"
}
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context | /contexts/Service |
      | @type    | Service           |
      | @id      | /services/1       |
      | libelle  | nouveau           |

  Scenario: AS not "admin" I can't patch a service
    Given I send an authentication token for gestionnaire
    When I add "Content-Type" header equal to "application/merge-patch+json"
    And I send a "PATCH" request to "/services/1" with body:
    """
{
      "libelle" : "nouveau"
}
    """
    Then the response status code should be 403