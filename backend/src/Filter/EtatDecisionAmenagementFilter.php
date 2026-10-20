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
use App\Entity\DecisionAmenagementExamens;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\TypeInfo\TypeIdentifier;

class EtatDecisionAmenagementFilter extends AbstractFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    public const string PROPERTY = 'etatDecisionAmenagement';

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
        return ['type' => 'string'];
    }

    public function getOpenApiParameters(MetadataParameter $parameter): Parameter|array|null
    {
        return new Parameter(name: self::PROPERTY, in: 'query', description: 'Etat Décision', required: false, schema: [
            'type' => 'string',
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
        $etats = [
            DecisionAmenagementExamens::ETAT_EDITE,
            DecisionAmenagementExamens::ETAT_EDITION_DEMANDEE,
            DecisionAmenagementExamens::ETAT_VALIDE,
            DecisionAmenagementExamens::ETAT_ATTENTE_VALIDATION_CAS,
        ];

        if ($property !== self::PROPERTY) {
            return;
        }

        if (!is_string($value) || !in_array($value, $etats)) {
            $queryBuilder->andWhere('1=2');
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $etatParam = $queryNameGenerator->generateParameterName('etat');

        $em = $queryBuilder->getEntityManager();

        // MAX(debut) pour CE bénéficiaire (tous états)
        $maxQb = $em->createQueryBuilder();
        $maxQb
            ->select('MAX(decMax.debut)')
            ->from(DecisionAmenagementExamens::class, 'decMax')
            ->andWhere(sprintf('decMax.beneficiaire = %s', $rootAlias));

        // EXISTS de la décision qui est à ce MAX(debut) et avec l'état demandé
        $existsQb = $em->createQueryBuilder();
        $existsQb
            ->select('1')
            ->from(DecisionAmenagementExamens::class, 'decLast')
            ->andWhere(sprintf('decLast.beneficiaire = %s', $rootAlias))
            ->andWhere(sprintf('decLast.debut = (%s)', $maxQb->getDQL()))
            ->andWhere(sprintf('decLast.etat = :%s', $etatParam));

        $queryBuilder->andWhere($queryBuilder->expr()->exists($existsQb->getDQL()))->setParameter($etatParam, $value);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            self::PROPERTY => [
                'property' => self::PROPERTY,
                'type' => TypeIdentifier::STRING->value,
                'required' => false,
                'openapi' => new Parameter(name: self::PROPERTY, in: 'query', description: 'Etat Décision', schema: [
                    'type' => 'string',
                ]),
            ],
        ];
    }
}
