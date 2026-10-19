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

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\JsonSchemaFilterInterface;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Parameter;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use App\Entity\Formation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Clock\ClockAwareTrait;
use Symfony\Component\TypeInfo\TypeIdentifier;

class InscriptionEnCoursFilter implements FilterInterface, JsonSchemaFilterInterface, OpenApiParameterFilterInterface
{
    use ClockAwareTrait;

    public const string PROPERTY = 'avecInscriptions';

    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $value = null;
        if (isset($context['parameter'])) {
            $value = $context['parameter']->getValue();
        } elseif (isset($context['filters'][self::PROPERTY])) {
            $value = $context['filters'][self::PROPERTY];
        }

        if ($value !== true && $value !== 'true' && $value !== 1 && $value !== '1') {
            return;
        }

        if ($resourceClass !== Formation::class) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $inscriptionsAlias = $queryNameGenerator->generateJoinAlias('inscriptions');
        $nowParam = $queryNameGenerator->generateParameterName('now');
        $now = $this->now();

        $queryBuilder
            ->join(sprintf('%s.inscriptions', $alias), $inscriptionsAlias)
            ->andWhere(sprintf('%s.debut <= :%s', $inscriptionsAlias, $nowParam))
            ->andWhere(sprintf('%s.fin >= :%s', $inscriptionsAlias, $nowParam))
            ->setParameter($nowParam, $now);
    }

    public function getSchema(Parameter $parameter): array
    {
        return ['type' => 'boolean'];
    }

    public function getOpenApiParameters(Parameter $parameter): OpenApiParameter|array|null
    {
        return new OpenApiParameter(
            name: self::PROPERTY,
            in: 'query',
            description: 'uniquement les formations avec inscription',
            required: false,
            schema: ['type' => 'boolean'],
            style: 'form',
            explode: true,
        );
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            self::PROPERTY => [
                'property' => self::PROPERTY,
                'type' => TypeIdentifier::BOOL->value,
                'required' => false,
                'openapi' => new OpenApiParameter(
                    name: 'avecInscriptions',
                    in: 'query',
                    description: 'uniquement les formations avec inscription',
                    allowReserved: false,
                    schema: ['type' => 'boolean'],
                ),
            ],
        ];
    }
}
