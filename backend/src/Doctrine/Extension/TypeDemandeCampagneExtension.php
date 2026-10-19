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
use Doctrine\ORM\QueryBuilder;

final class TypeDemandeCampagneExtension implements QueryCollectionExtensionInterface
{
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $typeDemandeId = $context['type_demande_id'] ?? $context['filters']['typedemandeid'] ?? null;
        if (null === $typeDemandeId) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $tdAlias = $queryNameGenerator->generateJoinAlias('typeDemande');
        $paramName = $queryNameGenerator->generateParameterName('typedemande');

        $queryBuilder
            ->join($rootAlias . '.typeDemande', $tdAlias)
            ->andWhere(sprintf('%s.id = :%s', $tdAlias, $paramName))
            ->setParameter($paramName, $typeDemandeId);
    }
}
