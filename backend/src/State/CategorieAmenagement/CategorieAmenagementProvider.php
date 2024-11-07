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

namespace App\State\CategorieAmenagement;

use ApiPlatform\Metadata\Operation;
use App\ApiResource\CategorieAmenagement;
use App\ApiResource\OptionReponse;
use App\State\AbstractEntityProvider;
use Override;

class CategorieAmenagementProvider extends AbstractEntityProvider
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
        return CategorieAmenagement::class;
    }

    #[Override] protected function getEntityClass(): string
    {
        return \App\Entity\CategorieAmenagement::class;
    }

    /**
     * @param \App\Entity\CategorieAmenagement $entity
     * @return CategorieAmenagement
     */
    #[Override] public function transform($entity): mixed
    {
        $resource = new CategorieAmenagement();
        $resource->id = $entity->getId();
        $resource->libelle = $entity->getLibelle();
        $resource->actif = $entity->isActif();

        return $resource;
    }

    /**
     * @param \App\Entity\CategorieAmenagement $entity
     * @return OptionReponse
     */
    public function transformIntoOptionReponse(\App\Entity\CategorieAmenagement $entity): OptionReponse
    {
        $resource = new OptionReponse();
        $resource->id = $entity->getId();
        $resource->libelle = $entity->getLibelle();

        return $resource;
    }

    #[Override] protected function registerTransformations(): void
    {
        $this->transformerService->addTransformation(
            from    : $this->getEntityClass(),
            to      : OptionReponse::class,
            callback: $this->transformIntoOptionReponse(...));

        parent::registerTransformations();
    }
}