<?php

namespace App\Tests;

class TagTest extends ApiTestCaseCustom
{
    public function testAdminCanCreateTag(): void
    {
        $client = $this->createClientWithCredentials('admin');

        $client->request('POST', '/tags', [
            'json' => [
                'libelle' => 'Nouveau Tag Test',
                'actif' => true,
                'categorie' => '/categories_tags/1',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json');
        $this->assertJsonContains([
            'libelle' => 'Nouveau Tag Test',
            'actif' => true,
            'categorie' => '/categories_tags/1',
        ]);
    }

    public function testAnonymousCannotCreateTag(): void
    {
        $client = static::createClient();
        $client->request('POST', '/tags', [
            'json' => [
                'libelle' => 'Tag Anonyme',
                'actif' => true,
                'categorie' => '/categories_tags/1',
            ],

            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testSortTagsWorks(): void
    {
        $client = $this->createClientWithCredentials('admin');

        $client->request('GET', '/tags?order[libelle]=asc');
        $this->assertResponseIsSuccessful();
        $dataAsc = $client->getResponse()->toArray();
        $itemsAsc = $dataAsc['hydra:member'];

        $client->request('GET', '/tags?order[libelle]=desc');
        $this->assertResponseIsSuccessful();
        $dataDesc = $client->getResponse()->toArray();
        $itemsDesc = $dataDesc['hydra:member'];

        if (count($itemsAsc) >= 2) {
            $firstAsc = $itemsAsc[0]['libelle'];
            $lastAsc = $itemsAsc[count($itemsAsc) - 1]['libelle'];
            $firstDesc = $itemsDesc[0]['libelle'];
            $this->assertEquals($lastAsc, $firstDesc);
        }
    }
}
