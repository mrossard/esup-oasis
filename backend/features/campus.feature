Feature: Campus

  As an authenticated user
  I can use CRUD operations on /campus according to my role

  Scenario: I need to be authenticated
    Given I send no authentication token
    When I send a "GET" request to "/campus/1"
    Then the response status code should be 401

  Scenario: As "admin" I can read /campus
    Given I send an authentication token for admin
    When I send a "GET" request to "/campus"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON should be valid according to this schema:
    """
{
  "@context": "/contexts/Campus",
  "@id": "/campus",
  "@type": "hydra:Collection"
}
    """

  Scenario: As "gestionnaire" I can read /campus
    Given I send an authentication token for gestionnaire
    When I send a "GET" request to "/campus"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON should be valid according to this schema:
    """
{
  "@context": "/contexts/Campus",
  "@id": "/campus",
  "@type": "hydra:Collection"
}
    """

  @modifyData
  Scenario: As "admin" I can write to /campus
    Given I send an authentication token for admin
    When I add "Content-Type" header equal to "application/ld+json"
    And I send a "POST" request to "/campus" with body:
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
  "@context": "/contexts/Campus",
  "@type": "Campus"
}
    """

  Scenario: As not admin I can't write to /campus
    Given I send an authentication token for gestionnaire
    When I add "Content-Type" header equal to "application/ld+json"
    And I send a "POST" request to "/campus" with body:
    """
{
      "libelle" : "nouveau",
      "actif" : true
}
    """
    Then the response status code should be 403

  @modifyData
  Scenario: As "admin" I can patch a campus
    Given I send an authentication token for admin
    When I add "Content-Type" header equal to "application/merge-patch+json"
    And I send a "PATCH" request to "/campus/1" with body:
    """
{
      "libelle" : "nouveau"
}
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON should be valid according to this schema:
    """
{
  "@context": "/contexts/Campus",
  "@type": "Campus",
  "@id" : "/campus/1",
  "libelle": "nouveau"
}
    """

  Scenario: As not "admin" I can't patch a campus
    Given I send an authentication token for gestionnaire
    When I add "Content-Type" header equal to "application/merge-patch+json"
    And I send a "PATCH" request to "/campus/1" with body:
    """
{
      "libelle" : "nouveau"
}
    """
    Then the response status code should be 403