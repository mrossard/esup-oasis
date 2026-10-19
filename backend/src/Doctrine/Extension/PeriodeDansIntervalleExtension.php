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

namespace App\Doctrine\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Evenement;
use App\Entity\InterventionForfait;
use App\Entity\PeriodeRH;
use App\State\PeriodeRH\PeriodeManager;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\QueryBuilder;

final readonly class PeriodeDansIntervalleExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private PeriodeManager $periodeManager,
    ) {}

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (!in_array($resourceClass, [Evenement::class, InterventionForfait::class], true)) {
            return;
        }

        $intervalle = $context['intervalle'] ?? $context['filters']['intervalle'] ?? null;
        if (!is_array($intervalle)) {
            return;
        }

        if (!array_key_exists('debut', $intervalle) || !array_key_exists('fin', $intervalle)) {
            return;
        }

        $periodesConcernees = $this->periodeManager->periodesDansIntervalle(
            $intervalle['debut'],
            $intervalle['fin'],
            versionFinanciere: true,
        );

        $alias = $queryBuilder->getRootAliases()[0];
        $fieldname = $resourceClass === Evenement::class ? 'periodePriseEnCompteRH' : 'periode';
        $periodeAlias = $queryNameGenerator->generateJoinAlias('periode');
        $periodeIdsParameter = $queryNameGenerator->generateParameterName('periodeIds');

        $queryBuilder
            ->join(sprintf('%s.%s', $alias, $fieldname), $periodeAlias)
            ->andWhere(sprintf('%s.id in (:%s)', $periodeAlias, $periodeIdsParameter))
            ->setParameter(
                key: $periodeIdsParameter,
                value: array_map(static fn(PeriodeRH $periode): ?int => $periode->getId(), $periodesConcernees),
                type: ArrayParameterType::INTEGER,
            );
    }
}
