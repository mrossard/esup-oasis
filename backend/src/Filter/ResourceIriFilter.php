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

declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\JsonSchemaFilterInterface;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Parameter;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use App\Filter\Util\IriToIdResolver;
use Doctrine\ORM\QueryBuilder;

final readonly class ResourceIriFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    private IriToIdResolver $resolver;

    public function __construct(IriConverterInterface $iriConverter)
    {
        $this->resolver = new IriToIdResolver($iriConverter);
    }

    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $parameter = $context['parameter'] ?? null;
        if ($parameter === null) {
            return;
        }

        $value = $parameter->getValue();
        if ($value === null) {
            return;
        }

        $property = $parameter->getProperty() ?? $parameter->getKey();
        $this->applyFilter($property, $value, $queryBuilder, $queryNameGenerator);
    }

    private function applyFilter(
        string $property,
        mixed $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $rawValues = is_array($value) ? $value : [$value];
        $ids = array_values(array_filter(
            array_map($this->resolver->resolve(...), $rawValues),
            static fn(?int $id): bool => $id !== null,
        ));

        if ($ids === []) {
            return;
        }

        [$targetAlias, $targetField] = $this->resolveTargetAliasAndField(
            $property,
            $queryBuilder,
            $queryNameGenerator,
        );

        $paramName = $queryNameGenerator->generateParameterName($property);

        $clause = is_array($value)
            ? sprintf('%s.%s IN (:%s)', $targetAlias, $targetField, $paramName)
            : sprintf('%s.%s = :%s', $targetAlias, $targetField, $paramName);

        $queryBuilder->andWhere($clause)->setParameter($paramName, is_array($value) ? $ids : $ids[0]);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function resolveTargetAliasAndField(
        string $property,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): array {
        $alias = $queryBuilder->getRootAliases()[0];
        if (!str_contains($property, '.')) {
            return [$alias, $property];
        }

        $parts = explode('.', $property);
        $finalField = array_pop($parts);

        foreach ($parts as $part) {
            $joinAlias = $queryNameGenerator->generateJoinAlias($part);
            $queryBuilder->join(sprintf('%s.%s', $alias, $part), $joinAlias);
            $alias = $joinAlias;
        }

        return [$alias, $finalField];
    }

    public function getSchema(Parameter $parameter): array
    {
        return ['type' => 'string'];
    }

    public function getOpenApiParameters(Parameter $parameter): OpenApiParameter|array|null
    {
        $property = $parameter->getProperty() ?? $parameter->getKey();
        return [
            new OpenApiParameter(
                name: $property,
                in: 'query',
                description: sprintf('Filtrer par %s (IRI ou ID)', $property),
                required: false,
                schema: ['type' => 'string'],
                style: 'form',
                explode: false,
            ),
            new OpenApiParameter(
                name: $property . '[]',
                in: 'query',
                description: sprintf('Filtrer par %s (IRI ou ID)', $property),
                required: false,
                schema: [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                style: 'form',
                explode: true,
            ),
        ];
    }

    public function getDescription(string $resourceClass): array
    {
        return [];
    }
}
