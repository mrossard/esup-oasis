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

namespace App\State\TypeEvenement;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\TypeEvenement;
use App\State\MappedEntityProcessor;
use ReflectionException;

readonly class TypeEvenementProcessor implements ProcessorInterface
{
    public function __construct(private MappedEntityProcessor $processor)
    {
    }

    /**
     * @param \App\ApiResource\TypeEvenement $data
     * @param Operation                      $operation
     * @param array                          $uriVariables
     * @param array                          $context
     * @return array|mixed|object|null
     * @throws ReflectionException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $context['existingEntities'] = ['tauxHoraires'];

        return $this->processor->process(
            data        : $data,
            operation   : $operation,
            entityClass : TypeEvenement::class,
            uriVariables: $uriVariables,
            context     : $context);
    }
}