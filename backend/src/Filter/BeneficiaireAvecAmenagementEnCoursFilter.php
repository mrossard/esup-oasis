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
use App\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Clock\ClockAwareTrait;
use Symfony\Component\TypeInfo\TypeIdentifier;

class BeneficiaireAvecAmenagementEnCoursFilter extends AbstractFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    use ClockAwareTrait;

    public const string PROPERTY = 'benefAvecAmenagementEnCours';

    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $parameter = $context['parameter'] ?? null;
        if ($parameter !== null) {
            $value = $parameter->getValue();
            if ($value !== null && $value !== '') {
                $this->filterProperty(
                    self::PROPERTY,
                    $value,
                    $queryBuilder,
                    $queryNameGenerator,
                    $resourceClass,
                    $operation,
                    $context,
                );
            }
            return;
        }

        if (isset($context['filters'][self::PROPERTY])) {
            $this->filterProperty(
                self::PROPERTY,
                $context['filters'][self::PROPERTY],
                $queryBuilder,
                $queryNameGenerator,
                $resourceClass,
                $operation,
                $context,
            );
        }
    }

    public function getSchema(MetadataParameter $parameter): array
    {
        return ['type' => 'boolean'];
    }

    public function getOpenApiParameters(MetadataParameter $parameter): Parameter|array|null
    {
        return new Parameter(name: self::class, in: 'query', description: self::class, required: false, schema: [
            'type' => 'boolean',
        ]);
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
        $entityClass = $operation->getStateOptions()?->getEntityClass() ?? $operation->getClass();
        if ($entityClass !== Utilisateur::class || $property !== self::PROPERTY) {
            return;
        }

        if ($value) {
            $alias = $queryBuilder->getRootAliases()[0];
            $benefAlias = $queryNameGenerator->generateJoinAlias('beneficiaires');
            $amenagementsAlias = $queryNameGenerator->generateJoinAlias('amenagements');
            $nowParam = $queryNameGenerator->generateParameterName('now');

            $queryBuilder
                ->join(sprintf('%s.beneficiaires', $alias), $benefAlias)
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

    public function getDescription(string $resourceClass): array
    {
        return [
            self::PROPERTY => [
                'property' => self::class,
                'type' => TypeIdentifier::BOOL->value,
                'required' => false,
                'is_collection' => false,
                'openapi' => new Parameter(name: self::class, in: 'query', description: self::class, schema: [
                    'type' => 'boolean',
                ]),
            ],
        ];
    }
}
