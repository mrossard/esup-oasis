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
use App\Entity\Evenement;
use App\Entity\InterventionForfait;
use App\Entity\PeriodeRH;
use App\Filter\Util\IriToIdResolver;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final readonly class ActivitePeriodeFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    private IriToIdResolver $resolver;

    public function __construct(
        private ManagerRegistry $managerRegistry,
        IriConverterInterface $iriConverter,
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

        $value = $parameter->getValue();
        if ($value === null || $value === '') {
            return;
        }

        $entityClass = $operation?->getStateOptions()?->getEntityClass() ?? $operation?->getClass() ?? $resourceClass;

        if (!in_array($entityClass, [Evenement::class, InterventionForfait::class], true)) {
            return;
        }

        $rawValues = is_array($value) ? $value : [$value];
        $ids = array_values(array_filter(
            array_map($this->resolver->resolve(...), $rawValues),
            static fn(?int $id): bool => $id !== null,
        ));

        if ($ids === []) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];

        if ($entityClass === InterventionForfait::class) {
            $paramName = $queryNameGenerator->generateParameterName('periode');
            $clause = is_array($value)
                ? sprintf('%s.periode IN (:%s)', $alias, $paramName)
                : sprintf('%s.periode = :%s', $alias, $paramName);

            $queryBuilder->andWhere($clause)->setParameter($paramName, is_array($value) ? $ids : $ids[0]);
            return;
        }

        // Evenement
        $em = $this->managerRegistry->getManagerForClass(PeriodeRH::class);
        $periodeRepo = $em->getRepository(PeriodeRH::class);

        $whereConditions = [];
        foreach ($ids as $periodeId) {
            /** @var PeriodeRH|null $periode */
            $periode = $periodeRepo->find($periodeId);
            if ($periode === null) {
                continue;
            }

            $periodeAlias = $queryNameGenerator->generateJoinAlias('periodePriseEnCompteRH');
            $periodeIdParameter = $queryNameGenerator->generateParameterName('periodeId');

            $queryBuilder->setParameter($periodeIdParameter, $periode->getId());

            if ($periode->getDateEnvoi() !== null) {
                $queryBuilder->leftJoin(
                    sprintf('%s.periodePriseEnCompteRH', $alias),
                    $periodeAlias,
                    Join::WITH,
                    sprintf('%s.id = :%s', $periodeAlias, $periodeIdParameter),
                );
                $whereConditions[] = sprintf('%s.id IS NOT NULL', $periodeAlias);
            } else {
                $queryBuilder->join(
                    PeriodeRH::class,
                    $periodeAlias,
                    Join::WITH,
                    sprintf('%s.id = :%s', $periodeAlias, $periodeIdParameter),
                );

                $whereConditions[] = sprintf(
                    '(%s.periodePriseEnCompteRH IS NULL AND %s.fin > %s.fin)',
                    $alias,
                    $periodeAlias,
                    $alias,
                );
            }
        }

        if ($whereConditions !== []) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(...$whereConditions));
        }
    }

    public function getSchema(Parameter $parameter): array
    {
        return ['type' => 'string'];
    }

    public function getOpenApiParameters(Parameter $parameter): OpenApiParameter|array|null
    {
        return [
            new OpenApiParameter(
                name: 'periode',
                in: 'query',
                description: 'Période RH concernée',
                required: false,
                schema: ['type' => 'string'],
                style: 'form',
                explode: false,
            ),
            new OpenApiParameter(
                name: 'periode[]',
                in: 'query',
                description: 'Périodes RH concernées',
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
