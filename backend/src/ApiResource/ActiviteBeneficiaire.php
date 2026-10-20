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

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\OpenApi\Model\Operation;
use App\Filter\ActivitePeriodeFilter;
use App\Filter\NestedUtilisateurFilter;
use App\Filter\ResourceIriFilter;
use App\State\Evenement\ActiviteBeneficiaireProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/suivis/beneficiaires',
            openapi: new Operation(tags: ['Suivis']),
            paginationEnabled: false,
            parameters: [
                'type' => new QueryParameter(filter: ResourceIriFilter::class, property: 'type'),
                'campus' => new QueryParameter(filter: ResourceIriFilter::class, property: 'campus'),
                'beneficiaires.profil' => new QueryParameter(
                    filter: ResourceIriFilter::class,
                    property: 'beneficiaires.profil',
                ),
                'beneficiaires.typologies' => new QueryParameter(
                    filter: ResourceIriFilter::class,
                    property: 'beneficiaires.typologies',
                ),
                'beneficiaires' => new QueryParameter(
                    filter: NestedUtilisateurFilter::class,
                    property: 'beneficiaires.utilisateur',
                ),
                'debut' => new QueryParameter(filter: new DateFilter(), property: 'debut'),
                'fin' => new QueryParameter(filter: new DateFilter(), property: 'fin'),
                'exists[dateAnnulation]' => new QueryParameter(filter: new ExistsFilter(), property: 'dateAnnulation'),
                'periode' => new QueryParameter(filter: ActivitePeriodeFilter::class, property: 'periode'),
            ],
        ),
        new Get(
            uriTemplate: '/suivis/beneficiaires/{id}',
            uriVariables: ['id'],
            status: 405,
            openapi: false,
            provider: [self::class, 'methodNotAllowed'],
        ),
    ],
    normalizationContext: ['groups' => self::OUT],
    security: 'is_granted("ROLE_GESTIONNAIRE")',
    provider: ActiviteBeneficiaireProvider::class,
    stateOptions: new Options(entityClass: \App\Entity\Evenement::class),
)]
class ActiviteBeneficiaire extends ActiviteUtilisateur
{
    public const string OUT = 'ActiviteBeneficiaire:out';
}
