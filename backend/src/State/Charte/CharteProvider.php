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

namespace App\State\Charte;

use App\ApiResource\Charte;
use App\Entity\ProfilBeneficiaire;
use App\State\AbstractEntityProvider;
use Exception;
use Override;

class CharteProvider extends AbstractEntityProvider
{

    #[Override] protected function getResourceClass(): string
    {
        return Charte::class;
    }

    #[Override] protected function getEntityClass(): string
    {
        return \App\Entity\Charte::class;
    }

    /**
     * @param \App\Entity\Charte $entity
     * @return Charte
     * @throws Exception
     */
    #[Override] public function transform($entity): mixed
    {
        $resource = new Charte();
        $resource->id = $entity->getId();
        $resource->libelle = $entity->getLibelle();
        $resource->contenu = $entity->getContenu();
        $resource->profilsAssocies = array_map(
            fn(ProfilBeneficiaire $profil) => $this->transformerService->transform(
                entity: $profil,
                to    : \App\ApiResource\ProfilBeneficiaire::class
            ),
            $entity->getProfilsAssocies()->toArray()
        );

        return $resource;
    }
}