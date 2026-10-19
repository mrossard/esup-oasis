<?php

/*
 * Copyright (c) 2026. Esup - Université de Bordeaux.
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 *  For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 *  @author Manuel Rossard <manuel.rossard@u-bordeaux.fr>
 *
 */

declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\JsonSchemaFilterInterface;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Parameter;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\TypeInfo\TypeIdentifier;

class TauxHoraireDateFilter implements FilterInterface, JsonSchemaFilterInterface, OpenApiParameterFilterInterface
{
    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $parameter = $context['parameter'] ?? null;
        $value = $parameter instanceof Parameter ? $parameter->getValue() : $context['filters']['date'] ?? null;

        if (null === $value || '' === $value) {
            return;
        }

        try {
            $date = new DateTime($value);
        } catch (Exception) {
            throw new HttpException(400, 'date mal formée');
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $paramName = $queryNameGenerator->generateParameterName('date');

        $queryBuilder->andWhere(sprintf(
            '%1$s.debut <= :%2$s AND (%1$s.fin >= :%2$s or %1$s.fin is null)',
            $rootAlias,
            $paramName,
        ))->setParameter($paramName, $date);
    }

    public function getSchema(Parameter $parameter): array
    {
        return ['type' => 'string'];
    }

    public function getOpenApiParameters(Parameter $parameter): OpenApiParameter|array|null
    {
        return new OpenApiParameter(
            name: $parameter->getKey(),
            in: 'query',
            description: 'date pour laquelle on veut le taux valide',
            required: false,
            schema: ['type' => 'string'],
        );
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'date' => [
                'property' => 'date',
                'type' => TypeIdentifier::STRING->value,
                'required' => false,
                'openapi' => new OpenApiParameter(
                    name: 'date',
                    in: 'query',
                    description: 'date pour laquelle on veut le taux valide',
                ),
            ],
        ];
    }
}
