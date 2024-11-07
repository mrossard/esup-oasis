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

namespace App\State\PeriodeRH;

use ApiPlatform\State\ProviderInterface;
use App\ApiResource\PeriodeRH;
use App\ApiResource\Utilisateur;
use App\State\AbstractEntityProvider;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PeriodeProvider extends AbstractEntityProvider
{

    function __construct(#[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')] ProviderInterface       $itemProvider,
                         #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')] ProviderInterface $collectionProvider)
    {

        parent::__construct($itemProvider, $collectionProvider);
    }

    /**
     * @param \App\Entity\PeriodeRH $entity
     * @return PeriodeRH
     * @throws Exception
     */
    public function transform($entity): PeriodeRH
    {
        $resource = new PeriodeRH();
        $resource->id = $entity->getId();
        $resource->debut = $entity->getDebut();
        $resource->fin = $entity->getFin();
        $resource->butoir = $entity->getButoir();
        $resource->dateEnvoi = $entity->getDateEnvoi();
        $resource->utilisateurEnvoi = match ($entity->getDateEnvoi()) {
            null => null,
            default => $this->transformerService->transform($entity->getUtilisateurEnvoi(), Utilisateur::class)
        };
        $resource->envoyee = (null !== $entity->getDateEnvoi());

        return $resource;
    }

    protected function getResourceClass(): string
    {
        return PeriodeRH::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\PeriodeRH::class;
    }
}