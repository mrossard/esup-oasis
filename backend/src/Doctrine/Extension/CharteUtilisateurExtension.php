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
use App\Entity\CharteDemandeur;
use Doctrine\ORM\QueryBuilder;

final class CharteUtilisateurExtension implements QueryCollectionExtensionInterface
{
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $entityClass = $operation?->getStateOptions()?->getEntityClass() ?? $resourceClass;
        if ($entityClass !== CharteDemandeur::class) {
            return;
        }

        $uid = $context['charte_utilisateur_uid'] ?? null;
        if (null === $uid) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $demandeAlias = $queryNameGenerator->generateJoinAlias('demande');
        $utilisateurAlias = $queryNameGenerator->generateJoinAlias('utilisateur');
        $uidParam = $queryNameGenerator->generateParameterName('uid');

        $queryBuilder
            ->join($alias . '.demande', $demandeAlias)
            ->innerJoin(sprintf('%s.demandeur', $demandeAlias), $utilisateurAlias)
            ->andWhere(sprintf('%s.uid = :%s', $utilisateurAlias, $uidParam))
            ->setParameter($uidParam, $uid);
    }
}
