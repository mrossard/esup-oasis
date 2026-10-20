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
use Symfony\Component\TypeInfo\TypeIdentifier;

class UtilisateurExistantSearchFilter extends AbstractFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    protected const string PROPERTY = 'recherche';

    public const array DEFAULT_PROPERTIES = [
        'nom' => 'string',
        'prenom' => 'string',
        'uid' => 'string',
        'numeroAnonyme' => 'int',
    ];

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
                $this->filterProperty(
                    static::PROPERTY,
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

        if (isset($context['filters'][static::PROPERTY])) {
            $this->filterProperty(
                static::PROPERTY,
                $context['filters'][static::PROPERTY],
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
        return new Parameter(
            name: static::PROPERTY,
            in: 'query',
            description: 'nom, prénom, uid ou numéro anonyme',
            required: false,
            schema: ['type' => 'string'],
        );
    }

    /**
     * @param string $property
     * @param string $value
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param Operation|null $operation
     * @param array $context
     * @return void
     */
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (static::PROPERTY !== $property) {
            return;
        }
        $alias = $queryBuilder->getRootAliases()[0];
        $strExprs = [];
        $intExprs = [];

        $properties = $this->properties ?? self::DEFAULT_PROPERTIES;
        if (array_key_exists(static::PROPERTY, $properties)) {
            $properties = self::DEFAULT_PROPERTIES;
        }
        foreach ($properties as $prop => $type) {
            if (($type ?? 'string') === 'string') {
                $strExprs[] = $queryBuilder->expr()->like(
                    'lower(unaccent(' . $alias . '.' . $prop . '))',
                    'unaccent(:strValue)',
                );
            } else {
                if (is_numeric($value)) {
                    $intExprs[] = sprintf('%s.%s = :intValue', $alias, $prop);
                }
            }
        }
        $orX = $queryBuilder->expr()->orX();
        $orX->addMultiple([...$strExprs, ...$intExprs]);

        $queryBuilder->andWhere($orX);
        if (count($strExprs) > 0) {
            $queryBuilder->setParameter('strValue', '%' . strtolower(trim($value)) . '%');
        }
        if (count($intExprs) > 0) {
            $queryBuilder->setParameter('intValue', $value);
        }
    }

    public function getDescription(string $resourceClass): array
    {
        $props = $this->properties ?? self::DEFAULT_PROPERTIES;
        if (array_key_exists(static::PROPERTY, $props)) {
            $props = self::DEFAULT_PROPERTIES;
        }
        return [
            'recherche' => [
                'property' => static::PROPERTY,
                'type' => TypeIdentifier::STRING->value,
                'required' => false,
                'openapi' => new Parameter(
                    name: static::PROPERTY,
                    in: 'query',
                    description: 'Recherche sur (' . implode(', ', array_keys($props)) . ')',
                ),
            ],
        ];
    }
}
