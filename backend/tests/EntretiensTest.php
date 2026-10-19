<?php

/*
 * Copyright (c) 2024-2026. Esup - Université de Bordeaux.
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 *  For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 *  @author Manuel Rossard <manuel.rossard@u-bordeaux.fr>
 *
 */

namespace App\Tests;

class EntretiensTest extends ApiTestCaseCustom
{
    public function testGestionnaireCanListEntretiens(): void
    {
        $client = $this->createClientWithCredentials('gestionnaire');
        $client->request('GET', '/utilisateurs/beneficiaire/entretiens');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/Entretien',
            '@id' => '/utilisateurs/beneficiaire/entretiens',
            '@type' => 'hydra:Collection',
        ]);
    }

    public function testGestionnaireCanCreateEntretien(): void
    {
        $client = $this->createClientWithCredentials('gestionnaire');
        $client->request('POST', '/utilisateurs/beneficiaire/entretiens', [
            'json' => [
                'commentaire' => 'Entretien de test',
                'date' => '2026-03-11T10:00:00+00:00',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'commentaire' => 'Entretien de test',
            'gestionnaire' => '/utilisateurs/gestionnaire',
        ]);
    }

    public function testBeneficiaireCanSeeOwnEntretiens(): void
    {
        $client = $this->createClientWithCredentials('beneficiaire');
        $client->request('GET', '/utilisateurs/beneficiaire/entretiens');

        $this->assertResponseIsSuccessful();
    }

    public function testBeneficiaireCannotSeeOtherEntretiens(): void
    {
        $client = $this->createClientWithCredentials('beneficiaire');
        $client->request('GET', '/utilisateurs/beneficiaire2/entretiens');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testBeneficiaireCannotCreateOwnEntretien(): void
    {
        $client = $this->createClientWithCredentials('beneficiaire');
        $client->request('POST', '/utilisateurs/beneficiaire/entretiens', [
            'json' => [
                'commentaire' => 'Mon propre entretien',
                'date' => '2026-03-11T10:00:00+00:00',
            ],
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testSortEntretiens(): void
    {
        $client = $this->createClientWithCredentials('gestionnaire');
        // Create an earlier entretien
        $client->request('POST', '/utilisateurs/beneficiaire/entretiens', [
            'json' => [
                'commentaire' => 'Entretien antérieur',
                'date' => '2025-01-01T10:00:00+00:00',
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);

        // Sort asc
        $response = $client->request('GET', '/utilisateurs/beneficiaire/entretiens?order[date]=asc');
        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertSame('2025-01-01T00:00:00+00:00', $data['hydra:member'][0]['date']);

        // Sort desc
        $response = $client->request('GET', '/utilisateurs/beneficiaire/entretiens?order[date]=desc');
        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertSame('2026-03-11T00:00:00+00:00', $data['hydra:member'][0]['date']);
    }
}
