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

final class PreloadAssociationsExtension implements QueryCollectionExtensionInterface
{
    public const string PROPERTY = 'preload_associations';

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $associations = $context[self::PROPERTY] ?? $context['filters']['preloadAssociations'] ?? null;
        if (!is_array($associations)) {
            return;
        }

        $aliases = [
            'root' => $queryBuilder->getRootAliases()[0],
        ];

        foreach ($associations as $associationName => $association) {
            $aliases[$associationName] = $queryNameGenerator->generateJoinAlias($associationName);
            if (($association['joinType'] ?? 'LEFT') === 'LEFT') {
                $queryBuilder->leftJoin(
                    $aliases[$association['sourceEntity']] . '.' . $association['relationName'],
                    $aliases[$associationName],
                );
            } else {
                $queryBuilder->innerJoin(
                    $aliases[$association['sourceEntity']] . '.' . $association['relationName'],
                    $aliases[$associationName],
                );
            }
            $queryBuilder->addSelect($aliases[$associationName]);
        }
    }
}
