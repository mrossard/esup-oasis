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

namespace App\State\Commission;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\MembreCommission;
use App\ApiResource\Utilisateur;
use App\ApiResource\Commission;
use App\Repository\CommissionRepository;
use App\Repository\MembreCommissionRepository;
use App\State\AbstractEntityProvider;
use App\State\Utilisateur\UtilisateurManager;
use Exception;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MembreCommissionProvider extends AbstractEntityProvider
{
    public function __construct(#[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')] ProviderInterface       $itemProvider,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')] ProviderInterface $collectionProvider,
                                private readonly MembreCommissionRepository                                                   $repository,
                                private readonly CommissionRepository                                                         $commissionRepository,
                                private readonly UtilisateurManager                                                           $utilisateurManager)
    {
        parent::__construct($itemProvider, $collectionProvider);
    }

    #[Override] public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof GetCollection) {
            return parent::provide($operation, $uriVariables, $context);
        }
        //on fait du custom pour le cas complexe
        $utilisateur = $this->utilisateurManager->parUid($uriVariables['uid']);
        $commission = $this->commissionRepository->find($uriVariables['commissionId']);

        $membre = $this->repository->findOneBy([
            'utilisateur' => $utilisateur,
            'commission' => $uriVariables['commissionId'],
        ]);

        return match ($membre) {
            null => $this->transformUtilisateur($utilisateur, $commission),
            default => $this->transform($membre)
        };

    }

    #[Override] protected function getResourceClass(): string
    {
        return MembreCommission::class;
    }

    #[Override] protected function getEntityClass(): string
    {
        return \App\Entity\MembreCommission::class;
    }

    /**
     * @param \App\Entity\MembreCommission $entity
     * @return MembreCommission
     * @throws Exception
     */
    #[Override] public function transform($entity): mixed
    {
        $resource = new MembreCommission();
        $resource->id = $entity->getId();
        $resource->utilisateur = $this->transformerService->transform($entity->getUtilisateur(), Utilisateur::class);
        $resource->commission = $this->transformerService->transform($entity->getCommission(), Commission::class);
        $resource->roles = $entity->getRoles();

        return $resource;
    }

    /**
     * @param \App\Entity\Utilisateur $utilisateur
     * @param \App\Entity\Commission  $commission
     * @return MembreCommission|null
     * @throws Exception
     */
    private function transformUtilisateur(\App\Entity\Utilisateur $utilisateur, \App\Entity\Commission $commission): ?MembreCommission
    {
        if (!$utilisateur->isAdmin() && !$utilisateur->isGestionnaire() && !$utilisateur->estRenfortDemandes()) {
            return null;
        }

        $resource = new MembreCommission();
        $resource->id = null;
        $resource->utilisateur = $this->transformerService->transform($utilisateur, Utilisateur::class);
        $resource->commission = $this->transformerService->transform($commission, Commission::class);
        if ($utilisateur->isAdmin() || $utilisateur->isGestionnaire()) {
            $resource->roles = [
                \App\Entity\Utilisateur::ROLE_MEMBRE_COMMISSION,
                \App\Entity\Utilisateur::ROLE_ATTRIBUER_PROFIL,
                \App\Entity\Utilisateur::ROLE_VALIDER_CONFORMITE_DEMANDE,
            ];
        } else {
            $resource->roles = [
                \App\Entity\Utilisateur::ROLE_MEMBRE_COMMISSION,
            ];
        }

        return $resource;
    }
}