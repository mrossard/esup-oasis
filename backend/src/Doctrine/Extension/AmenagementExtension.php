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
use App\Entity\Amenagement;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Clock\ClockAwareTrait;

final class AmenagementExtension implements QueryCollectionExtensionInterface
{
    use ClockAwareTrait;

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $entityClass = $operation?->getStateOptions()?->getEntityClass() ?? $operation?->getClass() ?? $resourceClass;
        if ($entityClass !== Amenagement::class) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $benefAlias = $queryNameGenerator->generateJoinAlias('beneficiaires');
        $nowParam = $queryNameGenerator->generateParameterName('now');

        $withCondition = sprintf(
            ':%s >= %s.debut and (:%s < %s.fin or %s.fin is null)',
            $nowParam,
            $benefAlias,
            $nowParam,
            $benefAlias,
            $benefAlias,
        );

        $uid = $context['amenagement_user_uid'] ?? $context['uri_variables']['uid'] ?? null;

        if ($uid !== null) {
            $utilisateurAlias = $queryNameGenerator->generateJoinAlias('utilisateur');
            $uidParam = $queryNameGenerator->generateParameterName('uid');

            $queryBuilder
                ->join(sprintf('%s.beneficiaires', $alias), $benefAlias, Join::WITH, $withCondition)
                ->innerJoin(sprintf('%s.utilisateur', $benefAlias), $utilisateurAlias)
                ->andWhere(sprintf('%s.uid = :%s', $utilisateurAlias, $uidParam))
                ->setParameter($uidParam, $uid)
                ->setParameter($nowParam, $this->now());
        } else {
            $queryBuilder->join(
                sprintf('%s.beneficiaires', $alias),
                $benefAlias,
                Join::WITH,
                $withCondition,
            )->setParameter($nowParam, $this->now());
        }
    }
}
