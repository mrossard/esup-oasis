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
use App\State\AvisEse\AvisEseDeleteProcessor;
use App\State\AvisEse\AvisEsePatchProcessor;
use App\State\AvisEse\AvisEsePostProcessor;
use App\State\AvisEse\AvisEseProvider;
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
            processor   : AvisEsePostProcessor::class
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
            processor   : AvisEsePatchProcessor::class,
        ),
        new Delete(
            uriTemplate : self::ITEM_URI,
            uriVariables: [
                'uid' => new Link(fromProperty: 'uid', toProperty: 'utilisateur', fromClass: Utilisateur::class),
                'id',
            ],
            processor   : AvisEseDeleteProcessor::class
        ),
    ],
    normalizationContext  : ['groups' => [self::GROUP_OUT]],
    denormalizationContext: ['groups' => [self::GROUP_IN]],
    provider              : AvisEseProvider::class,
    stateOptions          : new Options(entityClass: \App\Entity\AvisEse::class)

)]
#[ApiFilter(OrderFilter::class, properties: ['debut'])]
class AvisEse
{
    public const string COLLECTION_URI = Utilisateur::ITEM_URI . '/avis_ese';
    public const string ITEM_URI = self::COLLECTION_URI . '/{id}';
    public const string GROUP_IN = 'avis_ese:in';
    public const string GROUP_OUT = 'avis_ese:out';

    public Utilisateur $utilisateur;

    #[Groups([self::GROUP_OUT])]
    public ?int $id = null;
    #[Groups([self::GROUP_IN, self::GROUP_OUT])]
    public ?string $libelle = null;
    #[Groups([self::GROUP_IN, self::GROUP_OUT])]
    public ?string $commentaire = null;
    #[Groups([self::GROUP_IN, self::GROUP_OUT])]
    public DateTimeInterface $debut;
    #[Groups([self::GROUP_IN, self::GROUP_OUT])]
    public ?DateTimeInterface $fin;
    #[Groups([self::GROUP_IN, self::GROUP_OUT])]
    public ?Telechargement $fichier = null;
}