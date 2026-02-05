<?php

/*
 * Copyright (c) 2024. Esup - Université de Bordeaux.
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 *  For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 *  @author Manuel Rossard <manuel.rossard@u-bordeaux.fr>
 *
 */

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Charte\CharteProcessor;
use App\State\Charte\CharteProvider;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\ObjectMapper\Transform\MapCollection;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: self::COLLECTION_URI),
        new Get(uriTemplate: self::ITEM_URI, uriVariables: ['id']),
        new Post(uriTemplate: self::COLLECTION_URI),
        new Patch(uriTemplate: self::ITEM_URI, uriVariables: ['id']),
        new Delete(uriTemplate: self::ITEM_URI, uriVariables: ['id']),
    ],
    openapi: new Operation(tags: ['Referentiel']),
    stateOptions: new Options(entityClass: \App\Entity\Charte::class),
)]
#[Map(target: \App\Entity\Charte::class)]
class Charte
{
    public const string COLLECTION_URI = '/chartes';
    public const string ITEM_URI = '/chartes/{id}';

    #[ApiProperty(identifier: true)]
    #[Ignore]
    public ?int $id = null;

    public string $libelle;

    public string $contenu;

    /***
     * TODO: revoir ça pour mapping collections!
     */

    /**
     * @var ProfilBeneficiaire[]
     */
    #[Map(transform: new MapCollection())]
    public array $profilsAssocies;
}
