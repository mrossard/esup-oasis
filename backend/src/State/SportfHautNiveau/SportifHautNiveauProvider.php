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

namespace App\State\SportfHautNiveau;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\SportifHautNiveau;
use App\Repository\SportifHautNiveauRepository;
use App\State\AbstractEntityProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class SportifHautNiveauProvider extends AbstractEntityProvider
{
    public function __construct(#[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
                                private readonly ProviderInterface           $itemProvider,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
                                private readonly ProviderInterface           $collectionProvider,
                                private readonly SportifHautNiveauRepository $sportifHautNiveauRepository)
    {
        parent::__construct($this->itemProvider, $this->collectionProvider);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof Get) {
            $sportif = $this->sportifHautNiveauRepository->findOneBy([
                'identifiantExterne' => $uriVariables['identifiantExterne'],
            ]);
            return match ($sportif) {
                null => null, //404
                default => $this->transform($sportif)
            };
        }

        return parent::provide($operation, $uriVariables, $context);
    }

    protected function getResourceClass(): string
    {
        return SportifHautNiveau::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\SportifHautNiveau::class;
    }

    /**
     * @param \App\Entity\SportifHautNiveau $entity
     * @return mixed
     */
    public function transform($entity): mixed
    {
        $resource = new SportifHautNiveau();

        $resource->id = $entity->getId();
        $resource->identifiantExterne = $entity->getIdentifiantExterne();
        $resource->nom = $entity->getNom();
        $resource->prenom = $entity->getPrenom();
        $resource->anneeNaissance = $entity->getAnneeNaissance();

        return $resource;
    }
}