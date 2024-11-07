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

namespace App\State\Composante;

use App\ApiResource\Composante;
use App\ApiResource\Utilisateur;
use App\State\AbstractEntityProvider;
use Exception;

class ComposanteProvider extends AbstractEntityProvider
{

    protected function getResourceClass(): string
    {
        return Composante::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\Composante::class;
    }

    /**
     * @param \App\Entity\Composante $entity
     * @return Composante
     * @throws Exception
     */
    public function transform($entity): mixed
    {
        $resource = new Composante();
        $resource->id = $entity->getId();
        $resource->libelle = $entity->getLibelle();
        $resource->referents = array_values(array_map(
                fn($referent) => $this->transformerService->transform($referent, Utilisateur::class),
                $entity->getReferents()->toArray()
            )
        );
        return $resource;
    }
}