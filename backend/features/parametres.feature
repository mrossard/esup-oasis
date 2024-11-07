Feature: Parametres

  As an admin
  I can read/update /parametres[/{cle}/valeurs]

  Scenario: I need to have gestionnaire rights
    Given I send an authentication token for renfort
    When I send a GET request to "/parametres"
    Then the response status code should be 403

  Scenario: I can read all parametres
    Given I send an authentication token for gestionnaire
    When I send a GET request to "/parametres"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | @context | /contexts/Parametre |
      | @type    | hydra:Collection    |
      | @id      | /parametres         |

  @modifyData
  Scenario: I can set the value for a parametre
    Given I send an authentication token for admin
    And I add "Content-Type" header equal to "application/ld+json; charset=utf-8"
    When I send a POST request to "/parametres/FREQUENCE_RAPPELS/valeurs" with body:
    """
    {
    "valeur":"aaa",
    "debut": "2023-07-17T02:00:00.000Z",
    "fin": "2023-07-19T02:00:00.000Z"
    }
    """
    Then the response status code should be 201
    And the JSON nodes should contain:
      | @context | /contexts/ValeurParametre               |
      | @type    | ValeurParametre                         |
      | @id      | /parametres/FREQUENCE_RAPPELS/valeurs/4 |
      | valeur   | aaa                                     |

  @modifyData
  Scenario: I can modify a value
    Given I send an authentication token for admin
    And I add "Content-Type" header equal to "application/merge-patch+json"
    When I send a PATCH request to "/parametres/FREQUENCE_RAPPELS/valeurs/1" with body:
    """
    {
    "valeur":"bbb"
    }
    """
    Then the response status code should be 200
    And the JSON nodes should contain:
      | @context | /contexts/ValeurParametre               |
      | @type    | ValeurParametre                         |
      | @id      | /parametres/FREQUENCE_RAPPELS/valeurs/1 |
      | valeur   | bbb                                     |

