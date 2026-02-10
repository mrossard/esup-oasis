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

namespace App\State\AvisEse;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AvisEse;
use App\ApiResource\Telechargement;
use App\ApiResource\Utilisateur;

class AvisEseProvider implements ProviderInterface
{
    /**
     * @param \App\Entity\AvisEse $entity
     * @return AvisEse
     */
    public function transform($entity): AvisEse
    {
        $resource = new AvisEse();

        $resource->id = $entity->getId();
        $resource->utilisateur = $this->transformerService->transform($entity->getUtilisateur(), Utilisateur::class);
        $resource->libelle = $entity->getLibelle();
        $resource->commentaire = $entity->getCommentaire();
        $resource->debut = $entity->getDebut();
        $resource->fin = $entity->getFin();
        $resource->fichier = $this->transformerService->transform($entity->getFichier(), Telechargement::class);

        return $resource;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // TODO: Implement provide() method.
    }
}
