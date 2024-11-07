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
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Parametre\ParametreProvider;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations            : [
        new Get(
            uriTemplate : self::ITEM_URI,
            uriVariables: ['cle']
        ),
        new GetCollection(
            uriTemplate: self::COLLECTION_URI
        ),
    ],
    normalizationContext  : ['groups' => [self::GROUP_OUT]],
    denormalizationContext: ['groups' => [self::GROUP_IN]],
    openapi               : new Operation(tags: ['Referentiel']),
    order                 : ['cle' => 'ASC '],
    security              : 'is_granted("' . \App\Entity\Utilisateur::ROLE_GESTIONNAIRE . '")',
    provider              : ParametreProvider::class,
    stateOptions          : new Options(entityClass: \App\Entity\Parametre::class)
)]
class Parametre
{
    public const string ITEM_URI = '/parametres/{cle}';
    public const string COLLECTION_URI = '/parametres';
    public const string GROUP_IN = 'param:in';
    public const string GROUP_OUT = 'param:out';

    #[ApiProperty(identifier: true)]
    #[Groups(self::GROUP_OUT)]
    public string $cle;

    #[Groups(self::GROUP_OUT)]
    public bool $fichier = false;

    /**
     * @var ValeurParametre[]
     */
    #[Groups([self::GROUP_IN, self::GROUP_OUT])]
    #[ApiProperty(readableLink: false, writableLink: false)]
    public array $valeurs;

    /**
     * @var ValeurParametre[]
     */
    #[Groups(self::GROUP_OUT)]
    public array $valeursCourantes;
}