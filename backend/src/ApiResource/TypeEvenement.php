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

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\TypeEvenement\TypeEvenementProcessor;
use App\State\TypeEvenement\TypeEvenementProvider;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations            : [
        new GetCollection(
            uriTemplate: self::COLLECTION_URI,
            openapi    : new Operation(
                tags       : ['Referentiel'],
                summary    : "Liste des types d'événements",
                description: "Retourne la liste des types d'événements",
            )
        ),
        new Get(
            uriTemplate : self::ITEM_URI,
            uriVariables: ['id' => 'id'],
            openapi     : new Operation(
                tags       : ['Referentiel'],
                summary    : "Détail d'un types d'événements",
                description: "Retourne le détail du type d'événements demandé"
            )
        ),
        new Post(
            uriTemplate: self::COLLECTION_URI,
            security   : "is_granted('ROLE_ADMIN')",
        ),
        new Patch(
            uriTemplate: self::ITEM_URI,
            security   : "is_granted('ROLE_ADMIN')",
        ),
    ],
    normalizationContext  : ['groups' => self::GROUP_OUT],
    denormalizationContext: ['groups' => self::GROUP_IN],
    openapi               : new Operation(tags: ['Referentiel']),
    order                 : ['libelle' => 'ASC '],
    provider              : TypeEvenementProvider::class,
    processor             : TypeEvenementProcessor::class,
    stateOptions          : new Options(entityClass: \App\Entity\TypeEvenement::class)
)]
#[ApiFilter(BooleanFilter::class, properties: ['forfait'])]
final class TypeEvenement
{

    public const string ITEM_URI = '/types_evenements/{id}';
    public const string COLLECTION_URI = '/types_evenements';
    public const string GROUP_OUT = 'typesEvenements:out';
    public const string GROUP_IN = 'typesEvenements:in';

    #[ApiProperty(identifier: true)]
    #[Groups([self::GROUP_OUT])]
    public ?int $id = null;
    #[Assert\NotBlank]
    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public string $libelle;
    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public bool $actif = true;
    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public ?string $couleur;
    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public bool $visibleParDefaut = true;
    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public bool $avecValidation = false;

    /**
     * @var TauxHoraire[]
     */
    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    #[Assert\All([new Assert\Type(TauxHoraire::class)])]
    public array $tauxHoraires = [];

    #[Groups([self::GROUP_OUT])]
    public ?TauxHoraire $tauxActif = null;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public bool $forfait = false;

}