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
use App\ApiResource\Utilisateur as UtilisateurResource;
use App\Entity\TypeEvenement;
use App\Entity\Utilisateur;
use App\Filter\BeneficiaireAvecAmenagementEnCoursFilter;
use App\Filter\IntervenantFilter;
use App\Filter\RenfortFilter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Clock\ClockAwareTrait;

final class UtilisateurCollectionExtension implements QueryCollectionExtensionInterface
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
        if ($entityClass !== Utilisateur::class) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $uriTemplate = $operation?->getUriTemplate();

        // 1. /renforts
        if (
            $uriTemplate === UtilisateurResource::RENFORT_COLLECTION_URI
            || ($context['filters'][RenfortFilter::PROPERTY] ?? false)
        ) {
            $queryBuilder
                ->join($rootAlias . '.intervenant', 'i')
                ->join('i.typesEvenements', 'types')
                ->join($rootAlias . '.services', 'services')
                ->andWhere('types.id = :typeRenfort')
                ->setParameter('typeRenfort', TypeEvenement::TYPE_RENFORT);
            return;
        }

        // 2. /intervenants
        if (
            $uriTemplate === UtilisateurResource::INTERVENANT_COLLECTION_URI
            || ($context['filters'][IntervenantFilter::PROPERTY] ?? false)
        ) {
            $queryBuilder->join($rootAlias . '.intervenant', 'i')->innerJoin('i.typesEvenements', 'type');
            return;
        }

        // 3. /roles/{roleId}/utilisateurs
        $roleId = $context['uri_variables']['roleId'] ?? $context['filters']['role'] ?? null;
        if ($roleId !== null) {
            $roleParam = $queryNameGenerator->generateParameterName('role');
            $queryBuilder->andWhere(sprintf(
                'JSON_CONTAINS(%s.roles, :%s) = true',
                $rootAlias,
                $roleParam,
            ))->setParameter($roleParam, $roleId);
            return;
        }

        // 4. /amenagements/utilisateurs
        if (
            $uriTemplate === '/amenagements/utilisateurs'
            || ($context['filters'][BeneficiaireAvecAmenagementEnCoursFilter::PROPERTY] ?? false)
        ) {
            $benefAlias = $queryNameGenerator->generateJoinAlias('beneficiaires');
            $amenagementsAlias = $queryNameGenerator->generateJoinAlias('amenagements');
            $nowParam = $queryNameGenerator->generateParameterName('now');

            $queryBuilder
                ->join(sprintf('%s.beneficiaires', $rootAlias), $benefAlias)
                ->join(sprintf('%s.amenagements', $benefAlias), $amenagementsAlias)
                ->andWhere(sprintf(
                    '%1$s.debut <= :%2$s and (%1$s.fin is null or %1$s.fin > :%2$s)',
                    $amenagementsAlias,
                    $nowParam,
                ))
                ->andWhere(sprintf(
                    '%1$s.debut <= :%2$s and (%1$s.fin is null or %1$s.fin > :%2$s)',
                    $benefAlias,
                    $nowParam,
                ))
                ->setParameter($nowParam, $this->now());
        }
    }
}
