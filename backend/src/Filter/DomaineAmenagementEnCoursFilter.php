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
use Symfony\Component\TypeInfo\TypeIdentifier;

class DomaineAmenagementEnCoursFilter extends AbstractFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    public function __construct(
        private readonly UtilisateurAmenagementEnCoursFilterHelper $helper,
        ManagerRegistry $managerRegistry,
        ?LoggerInterface $logger = null,
        ?array $properties = null,
        ?NameConverterInterface $nameConverter = null,
    ) {
        parent::__construct(
            $managerRegistry,
            $logger,
            ['examens' => null, 'pedagogique' => null, 'aideHumaine' => null],
            $nameConverter,
        );
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
            if ($value !== null && $value !== '') {
                $property = $parameter->getProperty() ?? $parameter->getKey();
                $this->filterProperty(
                    $property,
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

        foreach (['examens', 'pedagogique', 'aideHumaine'] as $prop) {
            if (isset($context['filters'][$prop])) {
                $this->filterProperty(
                    $prop,
                    $context['filters'][$prop],
                    $queryBuilder,
                    $queryNameGenerator,
                    $resourceClass,
                    $operation,
                    $context,
                );
            }
        }
    }

    public function getSchema(MetadataParameter $parameter): array
    {
        return ['type' => 'boolean'];
    }

    public function getOpenApiParameters(MetadataParameter $parameter): Parameter|array|null
    {
        $property = $parameter->getProperty() ?? $parameter->getKey();
        return new Parameter(name: $property, in: 'query', description: $property, required: false, schema: [
            'type' => 'boolean',
        ]);
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
        if (!array_key_exists($property, $this->getProperties() ?? [])) {
            return;
        }

        $this->helper->ajouterJointuresDomaine($queryBuilder, $queryNameGenerator, $value, $property);
    }

    public function getDescription(string $resourceClass): array
    {
        $description = [];
        foreach (array_keys($this->getProperties() ?? []) as $property) {
            $description[$property] = [
                'property' => $property,
                'type' => TypeIdentifier::BOOL->value,
                'required' => false,
                'is_collection' => false,
                'openapi' => new Parameter(name: $property, in: 'query', description: $property, schema: [
                    'type' => 'boolean',
                ]),
            ];
        }
        return $description;
    }
}
