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
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Commission\MembreCommissionDeleteProcessor;
use App\State\Commission\MembreCommissionPutProcessor;
use App\State\Commission\MembreCommissionProvider;
use App\Validator\RoleCommissionValideConstraint;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ApiResource(
    operations          : [
        new GetCollection(
            uriTemplate : self::COLLECTION_URI,
            uriVariables: [
                'commissionId' => new Link(fromProperty: 'id', toProperty: 'commission', fromClass: Commission::class),
            ]
        ),
        new Get(
            uriTemplate : self::ITEM_URI,
            uriVariables: [
                'commissionId' => new Link(fromProperty: 'id', toProperty: 'commission', fromClass: Commission::class),
                'uid' => new Link(fromProperty: 'uid', toProperty: 'utilisateur', fromClass: Utilisateur::class),
            ]
        ),
        new Put(
            uriTemplate           : self::ITEM_URI,
            uriVariables          : [
                'commissionId' => new Link(fromProperty: 'id', toProperty: 'commission', fromClass: Commission::class),
                'uid' => new Link(fromProperty: 'uid', toProperty: 'utilisateur', fromClass: Utilisateur::class),
            ],
            denormalizationContext: ['groups' => [self::GROUP_IN]],
            processor             : MembreCommissionPutProcessor::class,
            allowCreate           : true

        ),
        new Delete(
            uriTemplate : self::ITEM_URI,
            uriVariables: [
                'commissionId' => new Link(fromProperty: 'id', toProperty: 'commission', fromClass: Commission::class),
                'uid' => new Link(fromProperty: 'uid', toProperty: 'utilisateur', fromClass: Utilisateur::class),
            ],
            processor   : MembreCommissionDeleteProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_OUT]],
    openapi             : new Operation(tags: ['Demandes', 'Utilisateurs']),
    provider            : MembreCommissionProvider::class,
    stateOptions        : new Options(entityClass: \App\Entity\MembreCommission::class)
)]
class MembreCommission
{
    public const string COLLECTION_URI = '/commissions/{commissionId}/membres';
    public const string ITEM_URI = '/commissions/{commissionId}/membres/{uid}';
    public const string GROUP_IN = 'membre_commission:in';
    public const string GROUP_OUT = 'membre_commission:out';

    #[Ignore] public ?int $id = null;

    #[Groups([self::GROUP_OUT])]
    public Utilisateur $utilisateur;

    #[Groups([self::GROUP_OUT])]
    public Commission $commission;

    /**
     * @var string[]
     */
    #[Groups([self::GROUP_IN, self::GROUP_OUT])]
    #[RoleCommissionValideConstraint]
    public array $roles;
}