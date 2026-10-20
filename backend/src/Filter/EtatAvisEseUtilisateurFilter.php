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
use App\Entity\AvisEse;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use RuntimeException;
use Symfony\Component\Clock\ClockAwareTrait;
use Symfony\Component\TypeInfo\TypeIdentifier;

class EtatAvisEseUtilisateurFilter extends AbstractFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    use ClockAwareTrait;

    protected const string PROPERTY = 'etatAvisEse';

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
        return new Parameter(name: self::PROPERTY, in: 'query', description: 'Etat avis ESE', required: false, schema: [
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
        $etats = [AvisEse::ETAT_EN_COURS, AvisEse::ETAT_EN_ATTENTE, AvisEse::ETAT_AUCUN];

        if ($property !== self::PROPERTY || !in_array($value, $etats)) {
            return;
        }

        /**
         * Les états possibles sont :
         * - EN_COURS
         * - EN_ATTENTE
         * - AUCUN
         *
         * Aucun => pas d'avis
         * En cours => avis valide avec PJ à l'instant T
         * En attente => avis sans PJ à l'instant T ou ancien avis valide
         */
        $alias = $queryBuilder->getRootAliases()[0];
        $avisAlias = $queryNameGenerator->generateJoinAlias('avis');
        $fichierAlias = $queryNameGenerator->generateJoinAlias('fichier');
        $nowParam = $queryNameGenerator->generateParameterName('now');

        $addFilters = match ($value) {
            AvisEse::ETAT_AUCUN => $this->addFilterAucun(...),
            AvisEse::ETAT_EN_ATTENTE => $this->addFilterEnAttente(...),
            AvisEse::ETAT_EN_COURS => $this->addFilterEnCours(...),
            default => throw new RuntimeException('impossible'),
        };

        $addFilters($queryBuilder, $queryNameGenerator, $alias, $avisAlias, $fichierAlias, $nowParam);
    }

    protected function addFilterAucun(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $rootAlias,
        string $avisAlias,
        string $fichierAlias,
        string $nowParam,
    ): void {
        $queryBuilder
            ->leftJoin(sprintf('%s.avisEse', $rootAlias), $avisAlias)
            ->andWhere(sprintf('%s.id is null', $avisAlias));
    }

    protected function addFilterEnCours(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $rootAlias,
        string $avisAlias,
        string $fichierAlias,
        string $nowParam,
    ): void {
        $queryBuilder
            ->leftJoin(sprintf('%s.avisEse', $rootAlias), $avisAlias)
            ->leftJoin(sprintf('%s.fichier', $avisAlias), $fichierAlias)
            ->setParameter($nowParam, $this->now())
            ->andWhere(sprintf(
                '%1$s.id is not null and (%2$s.fin > :%3$s or %2$s.fin is null)',
                $fichierAlias,
                $avisAlias,
                $nowParam,
            ));
    }

    protected function addFilterEnAttente(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $rootAlias,
        string $avisAlias,
        string $fichierAlias,
        string $nowParam,
    ): void {
        $avisEnCoursAlias = $queryNameGenerator->generateJoinAlias('avisEnCours');

        $queryBuilder
            ->join(sprintf('%s.avisEse', $rootAlias), $avisAlias)
            ->leftJoin(sprintf('%s.fichier', $avisAlias), $fichierAlias)
            ->leftJoin(
                sprintf('%s.avisEse', $rootAlias),
                $avisEnCoursAlias,
                Join::WITH,
                sprintf(
                    '%1$s.id is not null and (%2$s.fin > :%3$s or %2$s.fin is null)',
                    $fichierAlias,
                    $avisAlias,
                    $nowParam,
                ),
            )
            ->andWhere(sprintf(
                '%1$s.id is not null and (%2$s.id is null or %1$s.fin <= :%3$s)',
                $avisAlias,
                $fichierAlias,
                $nowParam,
            ))
            ->andWhere(sprintf('%s.id is null', $avisEnCoursAlias))
            ->setParameter($nowParam, $this->now());
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            self::PROPERTY => [
                'property' => self::PROPERTY,
                'type' => TypeIdentifier::STRING->value,
                'required' => false,
                'openapi' => new Parameter(name: self::PROPERTY, in: 'query', description: 'Etat avis ESE'),
            ],
        ];
    }
}
