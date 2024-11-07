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

namespace App\State\ParametreUI;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\ParametreUI;
use App\ApiResource\Utilisateur;
use App\Repository\ParametreUIRepository;
use App\State\AbstractEntityProvider;
use App\State\Utilisateur\UtilisateurManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ParametreUIProvider extends AbstractEntityProvider
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')] ProviderInterface       $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')] ProviderInterface $collectionProvider,
        private readonly ParametreUIRepository                                                        $parametreUIRepository,
        private readonly UtilisateurManager                                                           $utilisateurManager)
    {
        parent::__construct($itemProvider, $collectionProvider);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof GetCollection) {
            return parent::provide($operation, $uriVariables, $context);
        }

        $param = $this->parametreUIRepository->findOneBy([
            'utilisateur' => $this->utilisateurManager->parUid($uriVariables['uid']),
            'cle' => $uriVariables['cle'],
        ]);

        return match ($param) {
            null => null,
            default => $this->transform($param)
        };
    }

    protected function getResourceClass(): string
    {
        return ParametreUI::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\ParametreUI::class;
    }

    /**
     * @param \App\Entity\ParametreUI $entity
     * @return mixed
     */
    public function transform($entity): mixed
    {
        $resource = new ParametreUI();
        $resource->uid = $entity->getUtilisateur()->getUid();
        $resource->id = $entity->getId();
        $resource->cle = $entity->getCle();
        $resource->valeur = $entity->getValeur();
        $resource->utilisateur = $this->transformerService->transform($entity->getUtilisateur(), Utilisateur::class);

        return $resource;
    }
}