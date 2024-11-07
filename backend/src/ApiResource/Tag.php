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
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Filter\CaseInsensitiveOrderFilter;
use App\State\Tag\TagProcessor;
use App\State\Tag\TagProvider;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations            : [
        new GetCollection(
            uriTemplate: self::COLLECTION_URI
        ),
        new GetCollection(
            uriTemplate : CategorieTag::ITEM_URI . self::COLLECTION_URI,
            uriVariables: [
                'id' => new Link(fromProperty: 'id', toProperty: 'categorie', fromClass: CategorieTag::class),
            ]
        ),
        new Get(
            uriTemplate: self::ITEM_URI
        ),
        new Patch(
            uriTemplate: self::ITEM_URI
        ),
        new Post(
            uriTemplate: self::COLLECTION_URI
        ),
    ],
    normalizationContext  : ['groups' => [self::GROUP_OUT]],
    denormalizationContext: ['groups' => [self::GROUP_IN]],
    openapi               : new Operation(tags: ['Referentiel']),
    provider              : TagProvider::class,
    processor             : TagProcessor::class,
    stateOptions          : new Options(entityClass: \App\Entity\Tag::class)
)]
#[ApiFilter(CaseInsensitiveOrderFilter::class, properties: ['libelle'])]
class Tag
{
    public const string COLLECTION_URI = '/tags';
    public const string ITEM_URI = '/tags/{id}';
    public const string GROUP_IN = 'tag:in';
    public const string GROUP_OUT = 'tag:out';

    #[Groups([self::GROUP_OUT])]
    public ?int $id = null;
    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    #[Assert\NotBlank]
    public string $libelle;
    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public bool $actif = true;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public CategorieTag $categorie;

}