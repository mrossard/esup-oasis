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
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

final readonly class DemandeDisciplineSportiveFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    public const string PROPERTY = 'discipline';

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
            $value = $context['filters'][self::PROPERTY] ?? null;
        } else {
            $value = $parameter->getValue();
        }

        if ($value === null || $value === '') {
            return;
        }

        $rawValues = is_array($value) ? $value : [$value];
        $values = array_values(array_filter(
            array_map($this->resolver->resolve(...), $rawValues),
            static fn(?int $id): bool => $id !== null,
        ));

        if ($values === []) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $reponseAlias = $queryNameGenerator->generateJoinAlias('reponse');
        $disciplineAlias = $queryNameGenerator->generateJoinAlias('discipline');
        $demandeurAlias = $queryNameGenerator->generateJoinAlias('demandeur');
        $disciplineIdsParameter = $queryNameGenerator->generateParameterName('disciplineIdsParameter');

        $queryBuilder
            ->join(sprintf('%s.demandeur', $alias), $demandeurAlias)
            ->join(
                sprintf('%s.reponses', $demandeurAlias),
                $reponseAlias,
                Join::WITH,
                sprintf('%1$s.campagne = %2$s.campagne', $alias, $reponseAlias),
            )
            ->join(sprintf('%s.disciplinesSportives', $reponseAlias), $disciplineAlias)
            ->andWhere(sprintf(
                '%1$s.demandeur = %2$s.repondant and %1$s.campagne = %2$s.campagne',
                $alias,
                $reponseAlias,
            ))
            ->andWhere($queryBuilder->expr()->in(sprintf('%s.id', $disciplineAlias), ':' . $disciplineIdsParameter))
            ->setParameter($disciplineIdsParameter, $values);
    }

    public function getSchema(Parameter $parameter): array
    {
        return ['type' => 'string'];
    }

    public function getOpenApiParameters(Parameter $parameter): OpenApiParameter|array|null
    {
        return [
            new OpenApiParameter(
                name: self::PROPERTY,
                in: 'query',
                description: 'discipline sportive',
                required: false,
                schema: ['type' => 'string'],
                style: 'form',
                explode: false,
            ),
            new OpenApiParameter(
                name: self::PROPERTY . '[]',
                in: 'query',
                description: 'discipline sportive',
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
