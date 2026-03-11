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

class PiecesJointesTest extends ApiTestCaseCustom
{
    public function testGestionnaireCanListPiecesJointes(): void
    {
        $client = $this->createClientWithCredentials('gestionnaire');
        $client->request('GET', '/beneficiaires/beneficiaire/pieces_jointes');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/PieceJointeBeneficiaire',
            '@id' => '/beneficiaires/beneficiaire/pieces_jointes',
            '@type' => 'hydra:Collection',
        ]);
    }

    public function testBeneficiaireCanSeeOwnPiecesJointes(): void
    {
        $client = $this->createClientWithCredentials('beneficiaire');
        $client->request('GET', '/beneficiaires/beneficiaire/pieces_jointes');

        $this->assertResponseIsSuccessful();
    }

    public function testBeneficiaireCannotSeeOtherPiecesJointes(): void
    {
        $client = $this->createClientWithCredentials('beneficiaire');
        $client->request('GET', '/beneficiaires/beneficiaire2/pieces_jointes');

        $this->assertResponseStatusCodeSame(403);
    }
}
