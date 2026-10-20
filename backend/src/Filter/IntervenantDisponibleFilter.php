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

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\JsonSchemaFilterInterface;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Parameter as MetadataParameter;
use ApiPlatform\OpenApi\Model\Parameter;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\TypeInfo\TypeIdentifier;

class IntervenantDisponibleFilter extends AbstractFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $value = $context['filters']['creneau'] ?? null;
        if ($value === null && isset($context['parameter'])) {
            $value = $context['parameter']->getValue();
        }
        if ($value === null || $value === '' || $value === []) {
            return;
        }

        $this->filterProperty(
            'creneau',
            $value,
            $queryBuilder,
            $queryNameGenerator,
            $resourceClass,
            $operation,
            $context,
        );
    }

    public function getSchema(MetadataParameter $parameter): array
    {
        return ['type' => 'string'];
    }

    public function getOpenApiParameters(MetadataParameter $parameter): Parameter|array|null
    {
        return [
            new Parameter(
                name: 'creneau[debut]',
                in: 'query',
                description: 'début du créneau',
                required: false,
                schema: ['type' => 'string'],
                style: 'form',
                explode: true,
            ),
            new Parameter(
                name: 'creneau[fin]',
                in: 'query',
                description: 'fin du créneau',
                required: false,
                schema: ['type' => 'string'],
                style: 'form',
                explode: true,
            ),
        ];
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'creneau[debut]' => [
                'property' => 'creneau',
                'type' => TypeIdentifier::STRING->value,
                'required' => false,
                'openapi' => new Parameter(name: 'creneau', in: 'query', description: 'début du créneau'),
            ],
            'creneau[fin]' => [
                'property' => 'creneau',
                'type' => TypeIdentifier::STRING->value,
                'required' => false,
                'openapi' => new Parameter(name: 'creneau', in: 'query', description: 'fin du créneau'),
            ],
        ];
    }

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if ($property !== 'creneau') {
            return;
        }
        if (!array_key_exists('debut', $value) || !array_key_exists('fin', $value)) {
            //on sort en silence. log, message...erreur?
            return;
        }
        $alias = $queryBuilder->getRootAliases()[0];
        $intervenantAlias = $queryNameGenerator->generateJoinAlias('intervenant');
        $evenementsAlias = $queryNameGenerator->generateJoinAlias('evenements');

        $queryBuilder
            ->join($alias . '.intervenant', $intervenantAlias)
            ->leftJoin(
                $intervenantAlias . '.interventions',
                $evenementsAlias,
                Join::WITH,
                $evenementsAlias
                . '.debut between :debut and :fin or :debut between '
                . $evenementsAlias
                . '.debut and '
                . $evenementsAlias
                . '.fin',
            )
            ->andWhere($evenementsAlias . '.id is null')
            ->setParameter('debut', $value['debut'])
            ->setParameter('fin', $value['fin']);
    }
}
