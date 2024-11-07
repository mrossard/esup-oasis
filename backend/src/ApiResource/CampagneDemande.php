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
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\CampagneDemande\CampagneDemandeProcessor;
use App\State\CampagneDemande\CampagneDemandeProvider;
use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: self::ITEM_URI,
            uriVariables: ['id', 'typeId']
        ),
        new GetCollection(
            uriTemplate: self::COLLECTION_URI,
            uriVariables: ['typeId']
        ),
        new Post(
            uriTemplate: self::COLLECTION_URI,
            uriVariables: ['typeId'],
            security: "is_granted('ROLE_ADMIN')",
            read: false
        ),
        new Patch(
            uriTemplate: self::ITEM_URI,
            uriVariables: ['id', 'typeId'],
            security: "is_granted('ROLE_ADMIN')"
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_OUT]],
    denormalizationContext: ['groups' => [self::GROUP_IN]],
    openapi: new Operation(tags: ['Demandes']),
    provider: CampagneDemandeProvider::class,
    processor: CampagneDemandeProcessor::class,
    stateOptions: new Options(entityClass: \App\Entity\CampagneDemande::class),
)]
class CampagneDemande
{
    public const string COLLECTION_URI = '/types_demandes/{typeId}/campagnes';
    public const string ITEM_URI = '/types_demandes/{typeId}/campagnes/{id}';
    public const string GROUP_IN = 'campagne:in';
    public const string GROUP_OUT = 'campagne:out';

    #[Ignore]
    public ?int $typeId = null;

    #[ApiProperty(identifier: true)]
    #[Groups([self::GROUP_OUT])]
    public ?int $id = null;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public ?string $libelle;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public DateTimeInterface $debut;
    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public DateTimeInterface $fin;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public ?DateTimeInterface $dateCommission = null;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public ?DateTimeInterface $dateArchivage = null;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public ?Commission $commission = null;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public ?int $anneeCible = null;
}