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

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\JsonSchemaFilterInterface;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Parameter as MetadataParameter;
use ApiPlatform\OpenApi\Model\Parameter;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class TypeAmenagementEnCoursFilter extends AbstractFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    protected const string PROPERTY = 'type';

    public function __construct(
        private readonly UtilisateurAmenagementEnCoursFilterHelper $helper,
        ManagerRegistry $managerRegistry,
        ?LoggerInterface $logger = null,
        ?array $properties = null,
        ?NameConverterInterface $nameConverter = null,
    ) {
        parent::__construct($managerRegistry, $logger, $properties, $nameConverter);
    }

    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $parameter = $context['parameter'] ?? null;
        if ($parameter !== null) {
            $value = $parameter->getValue();
            if ($value !== null && $value !== '' && $value !== []) {
                $this->filterProperty(
                    self::PROPERTY,
                    $value,
                    $queryBuilder,
                    $queryNameGenerator,
                    $resourceClass,
                    $operation,
                    $context,
                );
            }
            return;
        }

        if (isset($context['filters'][self::PROPERTY])) {
            $this->filterProperty(
                self::PROPERTY,
                $context['filters'][self::PROPERTY],
                $queryBuilder,
                $queryNameGenerator,
                $resourceClass,
                $operation,
                $context,
            );
        }
    }

    public function getSchema(MetadataParameter $parameter): array
    {
        return ['type' => 'string'];
    }

    public function getOpenApiParameters(MetadataParameter $parameter): Parameter|array|null
    {
        return [
            new Parameter(
                name: self::PROPERTY,
                in: 'query',
                description: self::PROPERTY,
                required: false,
                schema: ['type' => 'string'],
                style: 'form',
                explode: true,
            ),
            new Parameter(
                name: self::PROPERTY . '[]',
                in: 'query',
                description: self::PROPERTY,
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

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if ($property !== self::PROPERTY) {
            return;
        }

        $this->helper->ajouterJointuresType($queryBuilder, $queryNameGenerator, $value);
    }

    public function getDescription(string $resourceClass): array
    {
        return $this->helper->getDescription(self::PROPERTY);
    }
}
