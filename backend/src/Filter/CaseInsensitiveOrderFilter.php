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
use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\NestedPropertyHelperTrait;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\JsonSchemaFilterInterface;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Parameter;
use ApiPlatform\Metadata\SortFilterInterface;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class CaseInsensitiveOrderFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface,
    SortFilterInterface,
    PropertyAwareFilterInterface
{
    use NestedPropertyHelperTrait;

    public function __construct(
        protected ?ManagerRegistry $managerRegistry = null,
        protected string $orderParameterName = 'order',
        ?LoggerInterface $logger = null,
        protected ?array $properties = null,
        protected ?NameConverterInterface $nameConverter = null,
    ) {}

    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $parameter = $context['parameter'] ?? null;
        if ($parameter instanceof Parameter) {
            $this->applyParameter($queryBuilder, $queryNameGenerator, $parameter);
            return;
        }

        $this->applyLegacyFilters($queryBuilder, $queryNameGenerator, $context['filters'] ?? []);
    }

    private function applyParameter(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        Parameter $parameter,
    ): void {
        $value = $parameter->getValue(null);
        if (!\is_string($value)) {
            return;
        }

        $direction = strtoupper($value);
        if (!\in_array($direction, ['ASC', 'DESC'], true)) {
            return;
        }

        $property = $parameter->getProperty();
        if (null === $property) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        [$alias, $field] = $this->addNestedParameterJoins(
            $property,
            $alias,
            $queryBuilder,
            $queryNameGenerator,
            $parameter,
            Join::LEFT_JOIN,
        );

        $this->addOrderClause($queryBuilder, $queryNameGenerator, $alias, $field, $direction);
    }

    private function applyLegacyFilters(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        array $filters,
    ): void {
        $orderValues = $filters[$this->orderParameterName] ?? null;
        if (!\is_array($orderValues)) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];

        foreach ($orderValues as $prop => $direction) {
            $dir = strtoupper((string) $direction);
            $this->addOrderClause(
                $queryBuilder,
                $queryNameGenerator,
                $alias,
                (string) $prop,
                \in_array($dir, ['ASC', 'DESC'], true) ? $dir : 'ASC',
            );
        }
    }

    private function addOrderClause(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $alias,
        string $field,
        string $direction,
    ): void {
        $join = sprintf('upper(unaccent(%s.%s))', $alias, $field);
        $fieldAlias = $queryNameGenerator->generateParameterName('order');
        $queryBuilder->addSelect($join . ' as hidden ' . $fieldAlias);
        $queryBuilder->addOrderBy($fieldAlias, $direction);
    }

    public function getSchema(Parameter $parameter): array
    {
        return [
            'type' => 'string',
            'default' => 'asc',
            'enum' => ['asc', 'desc'],
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
        $description = [];

        foreach ($this->properties ?? [] as $property => $propertyOptions) {
            $prop = \is_string($property) ? $property : (string) $propertyOptions;
            $defaultDir = \is_array($propertyOptions) ? $propertyOptions['default_direction'] ?? 'asc' : 'asc';

            $description[sprintf('%s[%s]', $this->orderParameterName, $prop)] = [
                'property' => $prop,
                'type' => 'string',
                'required' => false,
                'schema' => [
                    'type' => 'string',
                    'default' => strtolower((string) $defaultDir),
                    'enum' => [
                        strtolower(OrderFilterInterface::DIRECTION_ASC),
                        strtolower(OrderFilterInterface::DIRECTION_DESC),
                    ],
                ],
            ];
        }

        return $description;
    }

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function getProperties(): ?array
    {
        return $this->properties;
    }
}
