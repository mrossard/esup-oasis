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

use App\ApiResource\EtatDemande;
use App\State\AbstractEntityProvider;
use Override;

class EtatDemandeProvider extends AbstractEntityProvider
{

    #[Override] protected function getResourceClass(): string
    {
        return EtatDemande::class;
    }

    #[Override] protected function getEntityClass(): string
    {
        return \App\Entity\EtatDemande::class;
    }

    /**
     * @param \App\Entity\EtatDemande $entity
     * @return EtatDemande
     */
    #[Override] public function transform($entity): mixed
    {
        return new EtatDemande($entity->getId(), $entity->getLibelle());
    }
}