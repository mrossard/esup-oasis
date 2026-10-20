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
use ApiPlatform\Metadata\JsonSchemaFilterInterface;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Parameter;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use App\Entity\TypeEvenement;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class EvenementsAValiderFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    public function __construct(
        private Security $security,
    ) {}

    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $parameter = $context['parameter'] ?? null;
        if ($parameter === null) {
            $value = $context['filters']['aValider'] ?? null;
        } else {
            $value = $parameter->getValue();
        }

        if ($value === null || $value === '' || $value === false || $value === 'false' || $value === '0') {
            return;
        }

        $user = $this->security->getUser();
        $alias = $queryBuilder->getRootAliases()[0];
        $typeAlias = $queryNameGenerator->generateJoinAlias('type');

        $queryBuilder
            ->join(sprintf('%s.type', $alias), $typeAlias, Join::WITH, sprintf('%s.id = :typeRenfort', $typeAlias))
            ->andWhere(sprintf('%s.dateValidation IS NULL', $alias))
            ->setParameter('typeRenfort', TypeEvenement::TYPE_RENFORT);

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $renfortAlias = $queryNameGenerator->generateJoinAlias('utilisateurCreation');
            $renfortServiceAlias = $queryNameGenerator->generateJoinAlias('services');

            $queryBuilder
                ->join(sprintf('%s.utilisateurCreation', $alias), $renfortAlias)
                ->join(
                    sprintf('%s.services', $renfortAlias),
                    $renfortServiceAlias,
                    Join::WITH,
                    sprintf(':user MEMBER OF %s.utilisateurs', $renfortServiceAlias),
                )
                ->setParameter('user', $user);
        }
    }

    public function getSchema(Parameter $parameter): array
    {
        return ['type' => 'boolean'];
    }

    public function getOpenApiParameters(Parameter $parameter): OpenApiParameter|array|null
    {
        return new OpenApiParameter(
            name: 'aValider',
            in: 'query',
            description: 'uniquement les événements à valider ?',
            required: false,
            schema: ['type' => 'boolean'],
        );
    }

    public function getDescription(string $resourceClass): array
    {
        return [];
    }
}
