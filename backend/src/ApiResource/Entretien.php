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

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\State\Entretien\EntretienProcessor;
use App\State\Entretien\EntretienProvider;
use DateTimeInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations            : [
        new GetCollection(
            uriTemplate : self::COLLECTION_URI,
            uriVariables: [
                'uid' => new Link(fromProperty: 'uid', toProperty: 'utilisateur', fromClass: Utilisateur::class),
            ]
        ),
        new Post(
            uriTemplate : self::COLLECTION_URI,
            uriVariables: [
                'uid' => new Link(fromProperty: 'uid', toProperty: 'utilisateur', fromClass: Utilisateur::class),
            ],
            read        : false,
        ),
        new Get(
            uriTemplate : self::ITEM_URI,
            uriVariables: [
                'uid' => new Link(fromProperty: 'uid', toProperty: 'utilisateur', fromClass: Utilisateur::class),
                'id',
            ]
        ),
        new Patch(
            uriTemplate : self::ITEM_URI,
            uriVariables: [
                'uid' => new Link(fromProperty: 'uid', toProperty: 'utilisateur', fromClass: Utilisateur::class),
                'id',
            ],
        ),
        new Delete(
            uriTemplate : self::ITEM_URI,
            uriVariables: [
                'uid' => new Link(fromProperty: 'uid', toProperty: 'utilisateur', fromClass: Utilisateur::class),
                'id',
            ],
        ),
    ],
    normalizationContext  : ['groups' => [self::GROUP_OUT]],
    denormalizationContext: ['groups' => [self::GROUP_IN]],
    provider              : EntretienProvider::class,
    processor             : EntretienProcessor::class,
    stateOptions          : new Options(entityClass: \App\Entity\Entretien::class)
)]
#[ApiFilter(OrderFilter::class, properties: ['date'])]
class Entretien
{
    public const string COLLECTION_URI = Utilisateur::ITEM_URI . '/entretiens';
    public const string ITEM_URI = self::COLLECTION_URI . '/{id}';
    public const string GROUP_IN = 'entretien:in';
    public const string GROUP_OUT = 'entretien:out';

    public Utilisateur $utilisateur;

    #[Groups([self::GROUP_OUT])]
    public ?int $id = null;
    #[Groups([self::GROUP_IN, self::GROUP_OUT])]
    public ?string $commentaire = null;
    #[Groups([self::GROUP_IN, self::GROUP_OUT])]
    public DateTimeInterface $date;
    #[Groups([self::GROUP_IN, self::GROUP_OUT])]
    public ?Telechargement $fichier = null;
    #[Groups([self::GROUP_OUT])]
    public Utilisateur $gestionnaire;

}