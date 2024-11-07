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

namespace App\State\InterventionForfait;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\InterventionForfait;
use App\ApiResource\PeriodeRH;
use App\ApiResource\TypeEvenement;
use App\ApiResource\Utilisateur;
use App\Entity\Beneficiaire;
use App\State\AbstractEntityProvider;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class InterventionForfaitProvider extends AbstractEntityProvider
{

    public function __construct(#[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')] ProviderInterface       $itemProvider,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')] ProviderInterface $collectionProvider,
                                private readonly Security                                                                     $security)
    {
        parent::__construct($itemProvider, $collectionProvider);
    }

    protected function getResourceClass(): string
    {
        return InterventionForfait::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\InterventionForfait::class;
    }

    function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /**
         * Si non planificateur, on limite la liste aux interventions de la personne en tant qu'intervenant
         */
        if ($operation instanceof GetCollection) {
            //on ajoute "de force" un filtre sur l'utilisateur courant
            if (!$this->security->isGranted(\App\Entity\Utilisateur::ROLE_PLANIFICATEUR)) {
                $user = $this->security->getUser();
                $context['filters']['intervenant'] = Utilisateur::COLLECTION_URI . '/' . $user->getUserIdentifier();
            }
        }

        return parent::provide($operation, $uriVariables, $context);
    }

    /**
     * @param \App\Entity\InterventionForfait $entity
     * @return InterventionForfait
     * @throws Exception
     */
    public function transform($entity): InterventionForfait
    {
        $resource = new InterventionForfait();
        $resource->id = $entity->getId();
        $resource->intervenant = $this->transformerService->transform($entity->getIntervenant()->getUtilisateur(), Utilisateur::class);
        $resource->type = $this->transformerService->transform($entity->getType(), TypeEvenement::class);
        $resource->periode = $this->transformerService->transform($entity->getPeriode(), PeriodeRH::class);
        $resource->heures = $entity->getHeures();

        $resource->dateCreation = $entity->getDateCreation();
        $resource->utilisateurCreation = $this->transformerService->transform($entity->getUtilisateurCreation(), Utilisateur::class);

        if (null !== $entity->getDateModification()) {
            $resource->dateModification = $entity->getDateModification();
            $resource->utilisateurModification = $this->transformerService->transform($entity->getUtilisateurModification(), Utilisateur::class);
        }

        $resource->beneficiaires = array_values(
            array_reduce(
                array   : $entity->getBeneficiaires()->toArray(),
                callback: function ($carry, Beneficiaire $beneficiaire) {
                    if (!array_key_exists($beneficiaire->getUtilisateur()->getUid(), $carry)) {
                        $carry[$beneficiaire->getUtilisateur()->getUid()] = $this->transformerService->transform($beneficiaire->getUtilisateur(), Utilisateur::class);
                    }
                    return $carry;
                },
                initial : []
            )
        );

        return $resource;
    }
}