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
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\ApiResource\Utilisateur;
use App\Entity\Evenement;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

final readonly class UtilisateurConcerneParEvenementExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private IriConverterInterface $iriConverter,
    ) {}

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $entityClass = $operation?->getStateOptions()?->getEntityClass() ?? $operation?->getClass() ?? $resourceClass;
        if ($entityClass !== Evenement::class) {
            return;
        }

        $value = $context['filters']['utilisateurConcerne'] ?? $context['utilisateurConcerne'] ?? null;
        if ($value === null) {
            return;
        }

        if (!$value instanceof Utilisateur) {
            $utilisateur = $this->iriConverter->getResourceFromIri($value);
        } else {
            $utilisateur = $value;
        }

        if (in_array(\App\Entity\Utilisateur::ROLE_PLANIFICATEUR, $utilisateur->roles, true)) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $intervenantAlias = $queryNameGenerator->generateJoinAlias('intervenant');
        $beneficiairesAlias = $queryNameGenerator->generateJoinAlias('beneficiaires');
        $utilisateurAlias = $queryNameGenerator->generateJoinAlias('utilisateur');

        $queryBuilder
            ->leftJoin(sprintf('%s.intervenant', $alias), $intervenantAlias)
            ->leftJoin(sprintf('%s.beneficiaires', $alias), $beneficiairesAlias)
            ->join(
                \App\Entity\Utilisateur::class,
                $utilisateurAlias,
                Join::WITH,
                sprintf(
                    '(%s = %s.utilisateur or %s = %s.utilisateur) and %s.uid = :uid',
                    $utilisateurAlias,
                    $intervenantAlias,
                    $utilisateurAlias,
                    $beneficiairesAlias,
                    $utilisateurAlias,
                ),
            )
            ->setParameter('uid', $utilisateur->uid);
    }
}
