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
use App\State\OptionReponse\OptionReponseProvider;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ApiResource(
    operations  : [
        new Get(
            uriTemplate : self::ITEM_URI,
            uriVariables: ['id', 'questionId'],
            openapi     : false,
            provider    : OptionReponseProvider::class
        ),
    ],
    stateOptions: new Options(entityClass: \App\Entity\OptionReponse::class)
)]
class OptionReponse
{
    public const string ITEM_URI = '/questions/{questionId}/options/{id}';

    #[ApiProperty(identifier: true)]
    #[Groups([Question::GROUP_OUT, Reponse::GROUP_OUT, Demande::GROUP_OUT])]
    public ?int $id = null;

    #[Ignore]
    public int $questionId;

    #[Groups([Question::GROUP_OUT, Reponse::GROUP_OUT, Demande::GROUP_OUT])]
    public string $libelle;

    #[Groups([Question::GROUP_OUT, Reponse::GROUP_OUT])]
    public array $questionsLiees;
}