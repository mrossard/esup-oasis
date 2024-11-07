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
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Parametre\ValeurParametreProcessor;
use App\State\Parametre\ValeurParametreProvider;
use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations            : [
        new Get(
            uriTemplate : self::ITEM_URI,
            uriVariables: ['cle', 'id']
        ),
        new Post(
            uriTemplate : self::COLLECTION_URI,
            uriVariables: ['cle'],
            read        : false
        ),
        new Patch(
            uriTemplate : self::ITEM_URI,
            uriVariables: ['cle', 'id']
        ),
    ],
    normalizationContext  : ['groups' => [self::GROUP_OUT]],
    denormalizationContext: ['groups' => [self::GROUP_IN]],
    openapi               : new Operation(tags: ['Referentiel']),
    order                 : ['debut' => 'DESC '],
    provider              : ValeurParametreProvider::class,
    processor             : ValeurParametreProcessor::class,
    stateOptions          : new Options(entityClass: \App\Entity\ValeurParametre::class)
)]
#[Assert\Expression('this.fichier != null or this.valeur != null')]
class ValeurParametre
{
    public const string ITEM_URI = '/parametres/{cle}/valeurs/{id}';
    public const string COLLECTION_URI = '/parametres/{cle}/valeurs';
    public const string GROUP_IN = 'valeur_param:in';
    public const string GROUP_OUT = 'valeur_param:out';

    #[ApiProperty(identifier: true)]
    #[Groups([self::GROUP_OUT, Parametre::GROUP_OUT])]
    public ?int $id = null;

    //copie pour simplifier la gestion de l'IRI
    public string $cle;

    #[Groups([self::GROUP_OUT, self::GROUP_IN, Parametre::GROUP_OUT])]
    public ?string $valeur = null;

    #[Groups([self::GROUP_OUT, self::GROUP_IN, Parametre::GROUP_OUT])]
    public ?Telechargement $fichier = null;

    #[Groups([self::GROUP_OUT, self::GROUP_IN, Parametre::GROUP_OUT])]
    #[Assert\NotBlank]
    public DateTimeInterface $debut;

    #[Groups([self::GROUP_OUT, self::GROUP_IN, Parametre::GROUP_OUT])]
    #[Assert\GreaterThan(propertyPath: 'debut')]
    public ?DateTimeInterface $fin = null;

}