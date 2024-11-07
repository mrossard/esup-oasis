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
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Question\QuestionProvider;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations          : [
        new Get(
            openapi : new Operation(tags: ['Demandes']),
            provider: QuestionProvider::class
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_OUT]],
    stateOptions        : new Options(entityClass: \App\Entity\Question::class)
)]
class Question
{
    public const string GROUP_OUT = 'question:out';
    public const string COLLECTION_URI = '/questions';
    public const string ITEM_URI = '/questions/{id}';

    #[Groups([self::GROUP_OUT])]
    #[ApiProperty(identifier: true)]
    public ?int $id = null;

    #[Groups([self::GROUP_OUT])]
    public string $libelle;

    #[Groups([self::GROUP_OUT])]
    public ?string $aide;

    #[Groups([self::GROUP_OUT])]
    public string $typeReponse;

    #[Groups([self::GROUP_OUT])]
    public bool $obligatoire;

    #[Groups([self::GROUP_OUT])]
    public bool $choixMultiple;

    /**
     * @var OptionReponse[]
     */
    #[Groups([self::GROUP_OUT])]
    public array $optionsReponses;

    #[Groups([self::GROUP_OUT])]
    public ?string $tableOptions = null;

}