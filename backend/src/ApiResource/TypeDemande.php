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
use App\State\TypeDemande\TypeDemandeProcessor;
use App\State\TypeDemande\TypeDemandeProvider;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations            : [
        new GetCollection(
            uriTemplate: self::COLLECTION_URI,
        ),
        new Get(
            uriTemplate : self::ITEM_URI,
            uriVariables: ['id'],
        ),
        new Patch(
            uriTemplate : self::ITEM_URI,
            uriVariables: ['id'],
            security    : "is_granted('ROLE_ADMIN')",
        ),
        new Post(
            uriTemplate: self::COLLECTION_URI,
            security   : "is_granted('ROLE_ADMIN')",
            read       : false
        ),
    ],
    normalizationContext  : ['groups' => self::GROUP_OUT],
    denormalizationContext: ['groups' => self::GROUP_IN],
    openapi               : new Operation(tags: ['Demandes']),
    provider              : TypeDemandeProvider::class,
    processor             : TypeDemandeProcessor::class,
    stateOptions          : new Options(entityClass: \App\Entity\TypeDemande::class)
)]
#[ApiFilter(CaseInsensitiveOrderFilter::class, properties: ['libelle'])]
final class TypeDemande
{
    public const string COLLECTION_URI = '/types_demandes';
    public const string ITEM_URI = '/types_demandes/{id}';
    public const string GROUP_IN = 'type_demande:in';
    public const string GROUP_OUT = 'type_demande:out';

    #[ApiProperty(identifier: true)]
    #[Groups([self::GROUP_OUT])]
    public ?int $id = null;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public string $libelle;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public bool $actif = true;

    /**
     * @var ProfilBeneficiaire[]
     */
    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public array $profilsCibles;

    #[Groups([self::GROUP_OUT])]
    public ?CampagneDemande $campagneEnCours = null;
    #[Groups([self::GROUP_OUT])]
    public ?CampagneDemande $campagnePrecedente = null;
    #[Groups([self::GROUP_OUT])]
    public ?CampagneDemande $campagneSuivante = null;

    /**
     * @var EtapeDemande[]
     */
    #[Groups([self::GROUP_OUT])]
    public array $etapes;

    #[Groups([self::GROUP_IN, self::GROUP_OUT])]
    public bool $visibiliteLimitee = false;

    #[Groups([self::GROUP_IN, self::GROUP_OUT])]
    public bool $accompagnementOptionnel = false;

}