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

namespace App\State\ClubSportif;

use App\ApiResource\ClubSportif;
use App\ApiResource\OptionReponse;
use App\State\AbstractEntityProvider;
use Override;

class ClubSportifProvider extends AbstractEntityProvider
{

    #[Override] protected function getResourceClass(): string
    {
        return ClubSportif::class;
    }

    #[Override] protected function getEntityClass(): string
    {
        return \App\Entity\ClubSportif::class;
    }

    /**
     * @param \App\Entity\ClubSportif $entity
     * @return ClubSportif
     */
    #[Override] public function transform($entity): mixed
    {
        $resource = new ClubSportif();
        $resource->id = $entity->getId();
        $resource->libelle = $entity->getLibelle();
        $resource->actif = $entity->isActif();
        $resource->centreFormation = $entity->isCentreFormation();
        $resource->professionnel = $entity->isProfessionnel();

        return $resource;
    }

    /**
     * @param \App\Entity\ClubSportif $entity
     * @return OptionReponse
     */
    public function transformIntoOptionReponse(\App\Entity\ClubSportif $entity): OptionReponse
    {
        $resource = new OptionReponse();
        $resource->id = $entity->getId();
        $resource->libelle = $entity->getLibelle();
        $resource->questionsLiees = [];
        return $resource;
    }

    /**
     * @return void
     */
    #[Override] protected function registerTransformations(): void
    {
        $this->transformerService->addTransformation(
            from    : $this->getEntityClass(),
            to      : OptionReponse::class,
            callback: $this->transformIntoOptionReponse(...));
        parent::registerTransformations();
    }
}