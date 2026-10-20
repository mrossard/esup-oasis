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
use Doctrine\Persistence\ManagerRegistry;

final readonly class ResourceIriFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    private IriToIdResolver $resolver;

    public function __construct(
        IriConverterInterface $iriConverter,
        private ?ManagerRegistry $managerRegistry = null,
    ) {
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

        $property = $parameter->getProperty() ?? $parameter->getKey();
        $value = $context['filters'][$property] ?? $parameter->getValue();
        if ($value === null || $value === '') {
            return;
        }

        $entityClass = $operation?->getStateOptions()?->getEntityClass() ?? $operation?->getClass() ?? $resourceClass;
        $this->applyFilter($property, $value, $queryBuilder, $queryNameGenerator, $entityClass);
    }

    private function applyFilter(
        string $property,
        mixed $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $entityClass,
    ): void {
        $rawValues = is_array($value) ? $value : [$value];
        $ids = array_values(array_filter(
            array_map($this->resolver->resolve(...), $rawValues),
            static fn(?int $id): bool => $id !== null,
        ));

        if ($ids === []) {
            $queryBuilder->andWhere('1 = 0');
            return;
        }

        $target = $this->resolveTargetAliasAndField($property, $queryBuilder, $queryNameGenerator, $entityClass);

        if ($target === null) {
            return;
        }

        [$targetAlias, $targetField] = $target;

        $paramName = $queryNameGenerator->generateParameterName(str_replace('.', '_', $property));

        $clause = is_array($value)
            ? sprintf('%s.%s IN (:%s)', $targetAlias, $targetField, $paramName)
            : sprintf('%s.%s = :%s', $targetAlias, $targetField, $paramName);

        $queryBuilder->andWhere($clause)->setParameter($paramName, is_array($value) ? $ids : $ids[0]);
    }

    /**
     * @return array{0: string, 1: string}|null
     */
    private function resolveTargetAliasAndField(
        string $property,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $entityClass,
    ): ?array {
        $alias = $queryBuilder->getRootAliases()[0];
        $em = $this->managerRegistry?->getManagerForClass($entityClass);

        if ($em === null) {
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

        $currentMeta = $em->getClassMetadata($entityClass);
        $parts = explode('.', $property);
        $totalParts = count($parts);

        foreach ($parts as $i => $part) {
            $isLast = $i === ($totalParts - 1);

            if ($currentMeta->hasAssociation($part)) {
                $joinAlias = $queryNameGenerator->generateJoinAlias($part);
                $queryBuilder->join(sprintf('%s.%s', $alias, $part), $joinAlias);
                $alias = $joinAlias;
                $targetClass = $currentMeta->getAssociationTargetClass($part);
                $currentMeta = $em->getClassMetadata($targetClass);

                if ($isLast) {
                    $idField = $currentMeta->getSingleIdentifierFieldName();
                    return [$alias, $idField];
                }
            } elseif ($currentMeta->hasField($part)) {
                if ($isLast) {
                    return [$alias, $part];
                }
                return null;
            } else {
                return null;
            }
        }

        return [$alias, $currentMeta->getSingleIdentifierFieldName()];
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
