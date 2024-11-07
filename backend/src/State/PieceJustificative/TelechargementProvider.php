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

namespace App\State\PieceJustificative;

use App\ApiResource\Telechargement;
use App\ApiResource\Utilisateur;
use App\Entity\Fichier;
use App\State\AbstractEntityProvider;
use Exception;
use Override;

class TelechargementProvider extends AbstractEntityProvider
{

    #[Override] protected function getResourceClass(): string
    {
        return Telechargement::class;
    }

    #[Override] protected function getEntityClass(): string
    {
        return Fichier::class;
    }

    /**
     * @param Fichier $entity
     * @return Telechargement
     * @throws Exception
     */
    #[Override] public function transform($entity): mixed
    {
        $resource = new Telechargement();
        $resource->nom = $entity->getNom();
        $resource->typeMime = $entity->getTypeMime();
        $resource->id = $entity->getId();
        $resource->proprietaire = $this->transformerService->transform($entity->getProprietaire(), Utilisateur::class);
        $resource->urlContenu = '/fichiers/' . $resource->id;

        return $resource;
    }
}