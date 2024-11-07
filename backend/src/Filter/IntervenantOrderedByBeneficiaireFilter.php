<?php

/*
 * Copyright (c) 2024. Esup - Université de Bordeaux.
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 *  For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 *  @author Manuel Rossard <manuel.rossard@u-bordeaux.fr>
 *
 */

namespace App\Filter;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class IntervenantOrderedByBeneficiaireFilter extends AbstractFilter
{

    public function __construct(private readonly IriConverterInterface $iriConverter, ManagerRegistry $managerRegistry, LoggerInterface $logger = null, ?array $properties = null,
                                ?NameConverterInterface                $nameConverter = null)
    {
        parent::__construct($managerRegistry, $logger, $properties, $nameConverter);
    }

    protected function filterProperty(string                      $property, $value, QueryBuilder $queryBuilder,
                                      QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass,
                                      Operation                   $operation = null, array $context = []): void
    {
        if ($property !== 'beneficiaire') {
            return;
        }

        $utilisateur = $this->iriConverter->getResourceFromIri($value);

        $alias = $queryBuilder->getRootAliases()[0];
        $intervenantAlias = $queryNameGenerator->generateJoinAlias('intervenant');
        $evenementsAlias = $queryNameGenerator->generateJoinAlias('evenements');
        $benefAlias = $queryNameGenerator->generateJoinAlias('beneficiaires');
        $utilisateurAlias = $queryNameGenerator->generateJoinAlias('utilisateur');
        //on ne veut que les intervenants
        $queryBuilder
            ->addSelect($queryBuilder->expr()->countDistinct($evenementsAlias . '.id') . ' as hidden affinite')
            ->join($alias . '.intervenant', $intervenantAlias)
            ->leftJoin($intervenantAlias . '.interventions', $evenementsAlias)
            ->leftJoin($evenementsAlias . '.beneficiaires', $benefAlias)
            ->leftJoin($benefAlias . '.utilisateur', $utilisateurAlias, Join::WITH, ':uid =' . $utilisateurAlias . '.uid')
            ->setParameter('uid', $utilisateur->uid)
            ->addGroupBy($alias . '.id')
            ->addOrderBy('affinite', 'desc');
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'beneficiaire' => [
                'property' => 'beneficiaire',
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'openapi' => [
                    'description' => 'IRI utilisateur du beneficiaire concerné',
                    'name' => 'beneficiaire',
                    'type' => 'string',
                ],
            ],
        ];
    }
}