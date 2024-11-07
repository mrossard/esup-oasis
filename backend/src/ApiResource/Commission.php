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
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Filter\CaseInsensitiveOrderFilter;
use App\State\Commission\CommissionProcessor;
use App\State\Commission\CommissionProvider;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations            : [
        new Get(
            uriTemplate : self::ITEM_URI,
            uriVariables: ['id']
        ),
        new GetCollection(
            uriTemplate: self::COLLECTION_URI
        ),
        new Patch(
            uriTemplate: self::ITEM_URI,
            security   : "is_granted('ROLE_ADMIN')",
        ),
        new Post(
            uriTemplate: self::COLLECTION_URI,
            security   : "is_granted('ROLE_ADMIN')",
        ),
    ],
    normalizationContext  : ['groups' => [self::GROUP_OUT]],
    denormalizationContext: ['groups' => [self::GROUP_IN]],
    openapi               : new Operation(tags: ['Demandes']),
    provider              : CommissionProvider::class,
    processor             : CommissionProcessor::class,
    stateOptions          : new Options(entityClass: \App\Entity\Commission::class)
)]
#[ApiFilter(CaseInsensitiveOrderFilter::class, properties: ['libelle'])]
class Commission
{
    public const string COLLECTION_URI = '/commissions';
    public const string ITEM_URI = '/commissions/{id}';
    public const string GROUP_IN = 'commission:in';
    public const string GROUP_OUT = 'commission:out';

    #[ApiProperty(identifier: true)]
    #[Groups([self::GROUP_OUT])]
    public ?int $id = null;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public string $libelle;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public bool $actif = true;
}