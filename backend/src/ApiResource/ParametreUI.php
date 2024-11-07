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
use App\State\ParametreUI\ParametreUIProcessor;
use App\State\ParametreUI\ParametreUIProvider;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: self::COLLECTION_URI,
            uriVariables: [
                'uid' => new Link(fromProperty: 'uid', toProperty: 'utilisateur', fromClass: Utilisateur::class),
            ]
        ),
        new Get(
            uriTemplate: self::ITEM_URI,
            uriVariables: ['uid', 'cle']
        ),
        new Put(
            uriTemplate: self::ITEM_URI,
            uriVariables: ['uid', 'cle'],
            allowCreate: true,
        ),
        new Delete(
            uriTemplate: self::ITEM_URI,
            uriVariables: ['uid', 'cle'],
        ),
    ],
    openapi: new Operation(tags: ['Utilisateurs']),
    security: 'user.getUid() == request.get("uid")',
    provider: ParametreUIProvider::class,
    processor: ParametreUIProcessor::class,
    stateOptions: new Options(entityClass: \App\Entity\ParametreUI::class)
)]
class ParametreUI
{
    public const string COLLECTION_URI = '/utilisateurs/{uid}/parametres_ui';
    public const string ITEM_URI = '/utilisateurs/{uid}/parametres_ui/{cle}';

    #[Ignore] public ?int $id = null;
    #[Ignore] public string $uid;

    #[Ignore] public ?Utilisateur $utilisateur = null;

    #[Ignore] public string $cle;
    public string $valeur;
}