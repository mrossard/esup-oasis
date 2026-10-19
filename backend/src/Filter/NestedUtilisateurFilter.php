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

use ApiPlatform\Doctrine\Common\Filter\PropertyAwareFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\JsonSchemaFilterInterface;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Parameter;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\TypeInfo\TypeIdentifier;

class NestedUtilisateurFilter implements FilterInterface, PropertyAwareFilterInterface, JsonSchemaFilterInterface, OpenApiParameterFilterInterface
{
    /**
     * @param array<string, string>|null $properties
     */
    public function __construct(
        private readonly IriConverterInterface $iriConverter,
        protected ?ManagerRegistry $managerRegistry = null,
        ?LoggerInterface $logger = null,
        protected ?array $properties = null,
        ?NameConverterInterface $nameConverter = null,
    ) {}

    public function getProperties(): ?array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (isset($context['parameter'])) {
            $parameter = $context['parameter'];
            $value = $parameter->getValue();
            if (null === $value) {
                return;
            }
            $propertyKey = $parameter->getKey();
            $targetField = $parameter->getProperty() ?? ($this->properties[$propertyKey] ?? $propertyKey);
            $this->filterByTargetField($propertyKey, $targetField, $value, $queryBuilder, $queryNameGenerator, $resourceClass, $operation);
            return;
        }

        if (empty($context['filters']) || empty($this->properties)) {
            return;
        }

        foreach ($this->properties as $property => $targetField) {
            if (isset($context['filters'][$property])) {
                $this->filterByTargetField($property, $targetField, $context['filters'][$property], $queryBuilder, $queryNameGenerator, $resourceClass, $operation);
            }
        }
    }

    private function filterByTargetField(
        string $property,
        string $targetField,
        mixed $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
    ): void {
        if (!is_array($value)) {
            $value = [$value];
        }
        if (empty($value)) {
            return;
        }

        $utilisateurs = [];
        foreach ($value as $val) {
            try {
                $utilisateurs[] = $this->iriConverter->getResourceFromIri($val);
            } catch (\Throwable) {
                // Ignore invalid IRIs
            }
        }
        if (empty($utilisateurs)) {
            return;
        }

        $uidParams = [];
        foreach ($utilisateurs as $utilisateur) {
            $uidParam = $queryNameGenerator->generateParameterName('uid');
            $uidParams[] = $uidParam;
            $queryBuilder->setParameter($uidParam, $utilisateur->uid);
        }
        $uidInList = implode(' , ', array_map(static fn($p) => ':' . $p, $uidParams));

        $em = $queryBuilder->getEntityManager();
        $entityClass = $operation?->getStateOptions()?->getEntityClass() ?? $resourceClass;
        $currentEntityClass = $entityClass;
        $alias = $queryBuilder->getRootAliases()[0];

        $parts = explode('.', $targetField);
        $finalField = array_pop($parts);

        foreach ($parts as $part) {
            $nextAlias = $queryNameGenerator->generateJoinAlias($part);
            $queryBuilder->join(sprintf('%s.%s', $alias, $part), $nextAlias);
            $metadata = $em->getClassMetadata($currentEntityClass);
            $currentEntityClass = $metadata->getAssociationTargetClass($part);
            $alias = $nextAlias;
        }

        $joinAlias = $queryNameGenerator->generateJoinAlias($property);
        $queryBuilder->join(sprintf('%s.%s', $alias, $finalField), $joinAlias);
        $queryBuilder->andWhere($joinAlias . '.uid in (' . $uidInList . ')');
    }

    public function getSchema(Parameter $parameter): array
    {
        return ['type' => 'string'];
    }

    public function getOpenApiParameters(Parameter $parameter): array
    {
        $key = $parameter->getKey();
        return [
            new OpenApiParameter(
                name: $key,
                in: 'query',
                description: 'IRI utilisateur',
                required: false,
                schema: ['type' => 'string'],
                style: 'form',
                explode: true,
            ),
            new OpenApiParameter(
                name: $key . '[]',
                in: 'query',
                description: 'IRIs utilisateur',
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
        $description = [];
        foreach ($this->properties ?? [] as $property => $value) {
            $description[$property] = [
                'property' => $property,
                'type' => TypeIdentifier::STRING->value,
                'required' => false,
                'is_collection' => false,
                'openapi' => new OpenApiParameter(name: $property, in: 'query', description: 'IRI utilisateur'),
            ];
            $description[$property . '[]'] = [
                'property' => $property,
                'type' => TypeIdentifier::STRING->value,
                'required' => false,
                'is_collection' => true,
                'openapi' => new OpenApiParameter(name: $property, in: 'query', description: 'IRIs utilisateur'),
            ];
        }
        return $description;
    }
}

