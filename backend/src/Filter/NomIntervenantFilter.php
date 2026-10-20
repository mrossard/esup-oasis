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
use ApiPlatform\Metadata\JsonSchemaFilterInterface;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Parameter;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use Doctrine\ORM\QueryBuilder;

final readonly class NomIntervenantFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $parameter = $context['parameter'] ?? null;
        if ($parameter === null) {
            $value = $context['filters']['nomIntervenant'] ?? null;
        } else {
            $value = $parameter->getValue();
        }

        if ($value === null || $value === '') {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $joinAlias = $queryNameGenerator->generateJoinAlias('intervenant');
        $utilisateurAlias = $queryNameGenerator->generateJoinAlias('utilisateur');
        $paramName = $queryNameGenerator->generateParameterName('nomIntervenant');

        $queryBuilder
            ->join(sprintf('%s.intervenant', $alias), $joinAlias)
            ->join(sprintf('%s.utilisateur', $joinAlias), $utilisateurAlias)
            ->andWhere(sprintf('lower(unaccent(%s.nom)) LIKE unaccent(:%s)', $utilisateurAlias, $paramName))
            ->setParameter($paramName, '%' . strtolower((string) $value) . '%');
    }

    public function getSchema(Parameter $parameter): array
    {
        return ['type' => 'string'];
    }

    public function getOpenApiParameters(Parameter $parameter): OpenApiParameter|array|null
    {
        return new OpenApiParameter(
            name: 'nomIntervenant',
            in: 'query',
            description: "Recherche sur le nom de l'intervenant",
            required: false,
            schema: ['type' => 'string'],
        );
    }

    public function getDescription(string $resourceClass): array
    {
        return [];
    }
}
