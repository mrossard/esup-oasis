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
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\JsonSchemaFilterInterface;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Parameter;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use App\Entity\Evenement;
use App\Entity\InterventionForfait;
use App\Filter\Util\IriToIdResolver;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\QueryBuilder;

final readonly class BilanFinancierProfilBeneficiairesFilter implements
    FilterInterface,
    JsonSchemaFilterInterface,
    OpenApiParameterFilterInterface
{
    private IriToIdResolver $resolver;

    public function __construct(IriConverterInterface $iriConverter)
    {
        $this->resolver = new IriToIdResolver($iriConverter);
    }

    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (!in_array($resourceClass, [Evenement::class, InterventionForfait::class], true)) {
            return;
        }

        $value = $this->extractValue($context);
        if ($value === null) {
            return;
        }

        $rawValues = is_array($value) ? $value : [$value];
        $profilIds = array_values(array_filter(
            array_map($this->resolver->resolve(...), $rawValues),
            static fn(?int $id): bool => $id !== null,
        ));

        if ($profilIds === []) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $benefAlias = $queryNameGenerator->generateJoinAlias('beneficiaires');
        $profilAlias = $queryNameGenerator->generateJoinAlias('profil');
        $profilIdsParam = $queryNameGenerator->generateParameterName('profilIds');

        $queryBuilder
            ->join(sprintf('%s.beneficiaires', $alias), $benefAlias)
            ->join(sprintf('%s.profil', $benefAlias), $profilAlias)
            ->andWhere(sprintf('%s.id in (:%s)', $profilAlias, $profilIdsParam))
            ->setParameter($profilIdsParam, $profilIds, ArrayParameterType::INTEGER);
    }

    private function extractValue(array $context): mixed
    {
        $parameter = $context['parameter'] ?? null;
        if ($parameter !== null) {
            return $parameter->getValue();
        }

        return $context['filters']['profil'] ?? null;
    }

    public function getSchema(Parameter $parameter): array
    {
        return ['type' => 'string'];
    }

    public function getOpenApiParameters(Parameter $parameter): array
    {
        return [
            new OpenApiParameter(
                name: 'profil',
                in: 'query',
                description: 'IRI profil',
                required: false,
                schema: ['type' => 'string'],
                style: 'form',
                explode: true,
            ),
        ];
    }

    public function getDescription(string $resourceClass): array
    {
        return [];
    }
}
