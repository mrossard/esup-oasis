<?php

/*
 * Copyright (c) 2026. Esup - Université de Bordeaux.
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
use Symfony\Component\Clock\ClockAwareTrait;

final class CampagneNonArchiveeFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    use ClockAwareTrait;

    public const string PROPERTY = 'archivees';

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

        if ($value === null || !in_array($value, [false, 'false', 0, '0'], true)) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $campagneAlias = $queryNameGenerator->generateJoinAlias('campagne');

        $queryBuilder
            ->join(sprintf('%s.campagne', $rootAlias), $campagneAlias)
            ->andWhere(sprintf('%1$s.dateArchivage IS NULL or %1$s.dateArchivage >= :now', $campagneAlias))
            ->setParameter('now', $this->now());
    }

    public function getSchema(Parameter $parameter): array
    {
        return ['type' => 'boolean'];
    }

    public function getOpenApiParameters(Parameter $parameter): OpenApiParameter|array|null
    {
        return new OpenApiParameter(
            name: self::PROPERTY,
            in: 'query',
            description: 'inclure les demandes des campagnes archivées ?',
            required: false,
            schema: ['type' => 'boolean'],
        );
    }

    public function getDescription(string $resourceClass): array
    {
        return [];
    }
}
