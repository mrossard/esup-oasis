<?php

/*
 * Copyright (c) 2024-2026. Esup - Université de Bordeaux.
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 *  For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 *  @author Manuel Rossard <manuel.rossard@u-bordeaux.fr>
 *
 */

namespace App\Filter;

use ApiPlatform\Doctrine\Common\Filter\OrderFilterInterface;
use ApiPlatform\Doctrine\Common\Filter\PropertyAwareFilterInterface;
use ApiPlatform\Metadata\FilterInterface;
use ApiPlatform\Metadata\JsonSchemaFilterInterface;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\Parameter;
use ApiPlatform\Metadata\SortFilterInterface;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;

class ServicesFaitsPeriodeOrderFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface,
    SortFilterInterface,
    PropertyAwareFilterInterface
{
    /**
     * @param array<string, mixed>|null $properties
     */
    public function __construct(
        protected ?array $properties = ['debut' => null],
    ) {}

    public function getProperties(): ?array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function getSchema(Parameter $parameter): array
    {
        return [
            'type' => 'string',
            'enum' => [
                strtolower(OrderFilterInterface::DIRECTION_ASC),
                strtolower(OrderFilterInterface::DIRECTION_DESC),
            ],
        ];
    }

    public function getOpenApiParameters(Parameter $parameter): OpenApiParameter|array|null
    {
        return new OpenApiParameter(
            name: $parameter->getKey(),
            in: 'query',
            description: '',
            required: false,
            schema: $this->getSchema($parameter),
            style: 'form',
            explode: false,
        );
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'order[debut]' => [
                'property' => 'debut',
                'type' => 'string',
                'required' => false,
                'schema' => [
                    'type' => 'string',
                    'enum' => [
                        strtolower(OrderFilterInterface::DIRECTION_ASC),
                        strtolower(OrderFilterInterface::DIRECTION_DESC),
                    ],
                ],
            ],
        ];
    }
}
