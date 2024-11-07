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

namespace App\State\TypeSuiviAmenagement;

use ApiPlatform\Metadata\Operation;
use App\ApiResource\TypeSuiviAmenagement;
use App\State\AbstractEntityProvider;
use Override;

class TypeSuiviAmenagementProvider extends AbstractEntityProvider
{

    #[Override] public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        //on ajoute un tri par défaut sur les libellés
        if (null == ($context['filters']['order'] ?? null)) {
            $context['filters']['order']['libelle'] = 'asc';
        }

        return parent::provide($operation, $uriVariables, $context);
    }

    #[Override] protected function getResourceClass(): string
    {
        return TypeSuiviAmenagement::class;
    }

    #[Override] protected function getEntityClass(): string
    {
        return \App\Entity\TypeSuiviAmenagement::class;
    }

    /**
     * @param \App\Entity\TypeSuiviAmenagement $entity
     * @return TypeSuiviAmenagement
     */
    #[Override] public function transform($entity): mixed
    {
        $resource = new TypeSuiviAmenagement();
        $resource->id = $entity->getId();
        $resource->libelle = $entity->getLibelle();
        $resource->actif = $entity->isActif();

        return $resource;
    }
}