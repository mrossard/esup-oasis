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

namespace App\State\TypeEvenement;

use ApiPlatform\Metadata\Operation;
use App\ApiResource\TauxHoraire;
use App\ApiResource\TypeEvenement;
use App\State\AbstractEntityProvider;
use Exception;
use Override;
use Symfony\Component\Clock\ClockAwareTrait;

class TypeEvenementProvider extends AbstractEntityProvider
{

    use ClockAwareTrait;

    #[Override] public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        //on ajoute un tri par défaut sur les libellés
        if (null == ($context['filters']['order'] ?? null)) {
            $context['filters']['order']['libelle'] = 'asc';
        }

        return parent::provide($operation, $uriVariables, $context);
    }

    /**
     * @param \App\Entity\TypeEvenement $entity
     * @return TypeEvenement
     * @throws Exception
     */
    public function transform($entity): TypeEvenement
    {
        $resource = new TypeEvenement();
        $resource->id = $entity->getId();
        $resource->libelle = $entity->getLibelle();
        $resource->actif = $entity->isActif();
        $resource->couleur = $entity->getCouleur();
        $resource->avecValidation = $entity->isAvecValidation();
        $resource->visibleParDefaut = $entity->isVisibleParDefaut();
        $resource->forfait = $entity->isForfait();

        $resource->tauxHoraires = array_map(
            fn($taux) => $this->transformerService->transform($taux, TauxHoraire::class),
            $entity->getTauxHoraires()->toArray()
        );

        $resource->tauxActif = array_reduce($entity->getTauxHoraires()->toArray(),
            fn($carry, \App\Entity\TauxHoraire $taux) => match (true) {
                $taux->getDebut() <= $this->now() && (null == $taux->getFin() || $taux->getFin() > $this->now()) => $this->transformerService->transform($taux, TauxHoraire::class),
                default => null
            }
        );
        return $resource;
    }

    protected function getResourceClass(): string
    {
        return TypeEvenement::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\TypeEvenement::class;
    }
}