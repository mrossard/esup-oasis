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

namespace App\State\Entretien;

use App\ApiResource\Entretien;
use App\ApiResource\Telechargement;
use App\ApiResource\Utilisateur;
use App\State\AbstractEntityProvider;
use Exception;

class EntretienProvider extends AbstractEntityProvider
{

    protected function getResourceClass(): string
    {
        return Entretien::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\Entretien::class;
    }

    /**
     * @param \App\Entity\Entretien $entity
     * @return Entretien
     * @throws Exception
     */
    public function transform($entity): mixed
    {
        $resource = new Entretien();
        $resource->id = $entity->getId();
        $resource->date = $entity->getDate();
        $resource->commentaire = $entity->getCommentaire();
        $resource->fichier = $this->transformerService->transform($entity->getFichier(), Telechargement::class);
        $resource->utilisateur = $this->transformerService->transform($entity->getUtilisateur(), Utilisateur::class);
        $resource->gestionnaire = $this->transformerService->transform($entity->getGestionnaire(), Utilisateur::class);

        return $resource;
    }
}