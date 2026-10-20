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
use Symfony\Component\Clock\ClockAwareTrait;
use Symfony\Component\TypeInfo\TypeIdentifier;

class LibComposanteBeneficiaireFilter extends AbstractFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    use ClockAwareTrait;

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
                    'libelleComposante',
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

        if (isset($context['filters']['libelleComposante'])) {
            $this->filterProperty(
                'libelleComposante',
                $context['filters']['libelleComposante'],
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
            name: 'libelleComposante',
            in: 'query',
            description: "Recherche sur le libelle de composante d'inscription",
            required: false,
            schema: ['type' => 'string'],
        );
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
        if ($property !== 'libelleComposante') {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $inscriptionAlias = $queryNameGenerator->generateJoinAlias('inscriptions');
        $formationAlias = $queryNameGenerator->generateJoinAlias('formation');
        $composanteALias = $queryNameGenerator->generateJoinAlias('composante');

        $condition = $queryBuilder->expr()->like(
            'lower(unaccent(' . $composanteALias . '.libelle' . '))',
            'unaccent(?1)',
        );

        $queryBuilder
            ->join($alias . '.inscriptions', $inscriptionAlias)
            ->join($inscriptionAlias . '.formation', $formationAlias)
            ->join($formationAlias . '.composante', $composanteALias)
            ->andWhere($condition)
            ->andWhere()
            ->setParameter('1', '%' . strtolower($value) . '%');
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'libelleComposante' => [
                'property' => 'libelleComposante',
                'type' => TypeIdentifier::STRING->value,
                'required' => false,
                'openapi' => new Parameter(
                    name: 'libelleComposante',
                    in: 'query',
                    description: "Recherche sur le libelle de composante d'inscription",
                ),
            ],
        ];
    }
}
