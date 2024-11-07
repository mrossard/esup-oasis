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

namespace App\State\Inscription;

use App\ApiResource\Formation;
use App\ApiResource\Inscription;
use App\State\AbstractEntityProvider;
use Exception;

class InscriptionProvider extends AbstractEntityProvider
{

    protected function getResourceClass(): string
    {
        return Inscription::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\Inscription::class;
    }

    /**
     * @param \App\Entity\Inscription $entity
     * @return Inscription
     * @throws Exception
     */
    public function transform($entity): mixed
    {
        $resource = new Inscription();

        $resource->id = $entity->getId();
        $resource->formation = $this->transformerService->transform($entity->getFormation(), Formation::class);
        $resource->debut = $entity->getDebut();
        $resource->fin = $entity->getFin();

        return $resource;
    }
}