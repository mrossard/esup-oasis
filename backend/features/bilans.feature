Feature: Bilans

  As a user with ROLE_GESTIONNAIRE
  I can get predefined formatted excel extractions

  Scenario: I can get an extraction for "intervenants"
    Given I send an authentication token for gestionnaire
    When I send a GET request to "/suivis/intervenants"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | @context | /contexts/ActiviteIntervenant |
      | @type    | hydra:Collection              |
      | @id      | /suivis/intervenants          |
    And the JSON node "hydra:member" should exist
    And the JSON node "hydra:member[0].utilisateur" should exist
    And the JSON node "hydra:member[0].campus" should exist
    And the JSON node "hydra:member[0].type" should exist
    And the JSON node "hydra:member[0].tauxHoraire" should exist
    And the JSON node "hydra:member[0].nbHeures" should exist
    And the JSON node "hydra:member[0].nbEvenements" should exist

  Scenario: I can get an extraction for "beneficiaires"
    Given I send an authentication token for gestionnaire
    When I send a GET request to "/suivis/beneficiaires"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | @context | /contexts/ActiviteBeneficiaire |
      | @type    | hydra:Collection               |
      | @id      | /suivis/beneficiaires          |
    And the JSON node "hydra:member" should exist
    And the JSON node "hydra:member[0].utilisateur" should exist
    And the JSON node "hydra:member[0].campus" should exist
    And the JSON node "hydra:member[0].type" should exist
    And the JSON node "hydra:member[0].tauxHoraire" should exist
    And the JSON node "hydra:member[0].nbHeures" should exist
    And the JSON node "hydra:member[0].nbEvenements" should exist

  Scenario: I can filter results by PeriodeRH
    Given I send an authentication token for gestionnaire
    When I send a GET request to "/suivis/intervenants?periode[]=/periodes/1&periode[]=/periodes/2"
    Then the response status code should be 200