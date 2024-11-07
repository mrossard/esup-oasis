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

namespace App\State\TypeDemande;

use App\ApiResource\CampagneDemande;
use App\ApiResource\EtapeDemande;
use App\ApiResource\ProfilBeneficiaire;
use App\ApiResource\TypeDemande;
use App\State\AbstractEntityProvider;
use Exception;
use Symfony\Component\Clock\ClockAwareTrait;

class TypeDemandeProvider extends AbstractEntityProvider
{

    use ClockAwareTrait;

    protected function getResourceClass(): string
    {
        return TypeDemande::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\TypeDemande::class;
    }

    /**
     * @param \App\Entity\TypeDemande $entity
     * @return TypeDemande
     * @throws Exception
     */
    public function transform($entity): mixed
    {
        $resource = new TypeDemande();
        $resource->id = $entity->getId();
        $resource->libelle = $entity->getLibelle();
        $resource->actif = $entity->isActif();
        $resource->visibiliteLimitee = $entity->isVisibiliteLimitee();
        $resource->accompagnementOptionnel = $entity->isAccompagnementOptionnel();
        
        $resource->profilsCibles = array_map(
            callback: fn($profilEntity) => $this->transformerService->transform($profilEntity, ProfilBeneficiaire::class),
            array   : $entity->getProfilsAssocies()->toArray()
        );
        $now = $this->now();
        $precedente = null;
        $prochaine = null;
        foreach ($entity->getCampagnes() as $campagne) {
            //campagne en cours
            if ($campagne->getDebut() <= $now && $now <= $campagne->getFin()) {
                $resource->campagneEnCours = $this->transformerService->transform($campagne, CampagneDemande::class);
                continue;
            }
            //campagne terminée
            if ($campagne->getFin() < $now) {
                if ($precedente == null || $precedente->getFin() < $campagne->getFin()) {
                    $precedente = $campagne;
                }
                continue;
            }
            //campagne dans le futur...c'est la prochaine ?
            if ($prochaine === null || $prochaine->getDebut() > $campagne->getDebut()) {
                $prochaine = $campagne;
            }
        }
        $resource->campagnePrecedente = match (true) {
            null !== $precedente => $this->transformerService->transform($precedente, CampagneDemande::class),
            default => null
        };
        $resource->campagneSuivante = match (true) {
            null !== $prochaine => $this->transformerService->transform($prochaine, CampagneDemande::class),
            default => null
        };

        $resource->etapes = array_map(
            fn($etape) => $this->transformerService->transform($etape, EtapeDemande::class),
            $entity->getEtapes()->toArray()
        );

        return $resource;
    }
}