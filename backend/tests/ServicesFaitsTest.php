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

class ServicesFaitsTest extends ApiTestCaseCustom
{
    public function testIntervenantCanListOwnServicesFaits(): void
    {
        $client = $this->createClientWithCredentials('intervenant2');
        $client->request('GET', '/intervenants/intervenant2/services_faits');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/ServicesFaits',
            '@id' => '/intervenants/intervenant2/services_faits',
            'hydra:totalItems' => 1,
        ]);
    }

    public function testIntervenantCannotListOthersServicesFaits(): void
    {
        $client = $this->createClientWithCredentials('intervenant');
        $client->request('GET', '/intervenants/intervenant2/services_faits');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAdminCanListAnyIntervenantServicesFaits(): void
    {
        $client = $this->createClientWithCredentials('admin');
        $client->request('GET', '/intervenants/intervenant2/services_faits');

        $this->assertResponseIsSuccessful();
    }

    public function testGetIntervenantServiceFaitItem(): void
    {
        $client = $this->createClientWithCredentials('intervenant2');
        $client->request('GET', '/intervenants/intervenant2/services_faits/1');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/ServicesFaits',
            'id' => 1,
        ]);
    }

    public function testGetServiceFaitForSentPeriod(): void
    {
        $client = $this->createClientWithCredentials('gestionnaire');
        // periode 1 is sent in fixtures
        $client->request('GET', '/periodes/1/services_faits');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/ServicesFaits',
            'id' => 1,
        ]);
    }

    public function testCannotGetServiceFaitForNotSentPeriod(): void
    {
        $client = $this->createClientWithCredentials('gestionnaire');
        // periode 2 is NOT sent in fixtures
        $client->request('GET', '/periodes/2/services_faits');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testGetServiceFaitCsv(): void
    {
        $client = $this->createClientWithCredentials('gestionnaire');
        $client->request('GET', '/periodes/1/services_faits', [
            'headers' => ['Accept' => 'text/csv'],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'text/csv; charset=utf-8');
    }
}
