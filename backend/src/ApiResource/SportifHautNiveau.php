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

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Filter\CaseInsensitiveOrderFilter;
use App\State\SportfHautNiveau\SportifHautNiveauProcessor;
use App\State\SportfHautNiveau\SportifHautNiveauProvider;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;


#[ApiResource(
    operations          : [
        new GetCollection(
            uriTemplate: self::COLLECTION_URI
        ),
        new Get(
            uriTemplate : self::ITEM_URI,
            uriVariables: ['identifiantExterne']
        ),
        new Post(
            uriTemplate           : self::COLLECTION_URI,
            denormalizationContext: ['groups' => [self::GROUP_POST]],
            read                  : false
        ),
        new Patch(
            uriTemplate           : self::ITEM_URI,
            uriVariables          : ['identifiantExterne'],
            denormalizationContext: ['groups' => [self::GROUP_PATCH]]
        ),
        new Delete(
            uriTemplate : self::ITEM_URI,
            uriVariables: ['identifiantExterne']
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_OUT]],
    openapi             : new Operation(tags: ['Referentiel']),
    security            : "is_granted('ROLE_ADMIN')",
    provider            : SportifHautNiveauProvider::class,
    processor           : SportifHautNiveauProcessor::class,
    stateOptions        : new Options(entityClass: \App\Entity\SportifHautNiveau::class)
)]
#[ApiFilter(CaseInsensitiveOrderFilter::class, properties: ['nom'])]
#[ApiFilter(SearchFilter::class, properties: [
    'nom' => 'ipartial',
    'prenom' => 'ipartial',
    'identifiantExterne' => 'ipartial',
])]
class SportifHautNiveau
{
    public const string COLLECTION_URI = '/sportifs_haut_niveau';
    public const string ITEM_URI = '/sportifs_haut_niveau/{identifiantExterne}';

    public const string GROUP_OUT = 'sportif_haut_niveau:out';
    public const string GROUP_POST = 'sportif_haut_niveau:post';
    public const string GROUP_PATCH = 'sportif_haut_niveau:patch';

    #[Ignore] public ?int $id = null;

    #[ApiProperty(identifier: true)]
    #[Groups([self::GROUP_OUT, self::GROUP_POST])]
    public string $identifiantExterne;

    #[Groups([self::GROUP_PATCH, self::GROUP_POST, self::GROUP_OUT])]
    public ?string $nom = null;
    #[Groups([self::GROUP_PATCH, self::GROUP_POST, self::GROUP_OUT])]
    public ?string $prenom = null;
    #[Groups([self::GROUP_PATCH, self::GROUP_POST, self::GROUP_OUT])]
    public ?int $anneeNaissance = null;


}