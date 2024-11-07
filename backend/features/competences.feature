Feature: Competences

  As an authenticated user
  I can use CRUD operations on /competences according to my role

  Scenario: I need to be authenticated
    Given I send no authentication token
    When I send a "GET" request to "/competences"
    Then the response status code should be 401

  Scenario: I can read /competences as "admin"
    Given I send an authentication token for admin
    When I send a "GET" request to "/competences"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON should be valid according to this schema:
    """
{
  "@context": "/contexts/Competences",
  "@id": "/competences",
  "@type": "hydra:Collection"
}
    """

  Scenario: I can read /competences as "gestionnaire"
    Given I send an authentication token for gestionnaire
    When I send a "GET" request to "/competences"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON should be valid according to this schema:
    """
{
  "@context": "/contexts/Competences",
  "@id": "/competences",
  "@type": "hydra:Collection"
}
    """

  @modifyData
  Scenario: I can write to /competences as "admin"
    Given I send an authentication token for admin
    When I add "Content-Type" header equal to "application/ld+json"
    And I send a "POST" request to "/competences" with body:
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
  "@context": "/contexts/Competences",
  "@type": "Competence"
}
    """

  Scenario: As not "admin" I can't write to /competences
    Given I send an authentication token for gestionnaire
    When I add "Content-Type" header equal to "application/ld+json"
    And I send a "POST" request to "/competences" with body:
    """
{
      "libelle" : "nouveau",
      "actif" : true
}
    """
    Then the response status code should be 403