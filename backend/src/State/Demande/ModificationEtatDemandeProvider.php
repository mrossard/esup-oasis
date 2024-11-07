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

namespace App\State\Demande;

use ApiPlatform\Metadata\Operation;
use App\ApiResource\Demande;
use App\ApiResource\EtatDemande;
use App\ApiResource\ModificationEtatDemande;
use App\ApiResource\ProfilBeneficiaire;
use App\ApiResource\Utilisateur;
use App\State\AbstractEntityProvider;
use Exception;
use Override;

class ModificationEtatDemandeProvider extends AbstractEntityProvider
{

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return parent::provide($operation, $uriVariables, $context);
    }

    #[Override] protected function getResourceClass(): string
    {
        return ModificationEtatDemande::class;
    }

    #[Override] protected function getEntityClass(): string
    {
        return \App\Entity\ModificationEtatDemande::class;
    }

    /**
     * @param \App\Entity\ModificationEtatDemande $entity
     * @return ModificationEtatDemande
     * @throws Exception
     */
    #[Override] public function transform($entity): mixed
    {
        $resource = new ModificationEtatDemande();
        $resource->id = $entity->getId();
        $resource->commentaire = $entity->getCommentaire();
        $resource->etat = $this->transformerService->transform(
            $entity->getEtat(),
            EtatDemande::class
        );
        $resource->etatPrecedent = $this->transformerService->transform(
            $entity->getEtatPrecedent(),
            EtatDemande::class
        );
        $resource->profil = match ($profil = $entity->getProfil()) {
            null => null,
            default => $this->transformerService->transform(
                $profil,
                ProfilBeneficiaire::class
            ),
        };
        $resource->utilisateurModification = $this->transformerService->transform(
            $entity->getUtilisateur(),
            Utilisateur::class
        );
        $resource->demande = $this->transformerService->transform(
            $entity->getDemande(),
            Demande::class
        );

        $resource->dateModification = $entity->getDateModification();

        return $resource;
    }
}