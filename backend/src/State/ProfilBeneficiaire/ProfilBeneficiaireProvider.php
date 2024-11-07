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

namespace App\State\ProfilBeneficiaire;

use ApiPlatform\State\ProviderInterface;
use App\ApiResource\ProfilBeneficiaire;
use App\State\AbstractEntityProvider;
use App\State\MappedEntityTransformer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ProfilBeneficiaireProvider extends AbstractEntityProvider
{

    public function __construct(#[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')] ProviderInterface       $itemProvider,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')] ProviderInterface $collectionProvider,
                                private readonly MappedEntityTransformer                                                      $transformer)
    {

        parent::__construct($itemProvider, $collectionProvider);
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function transform($entity): ProfilBeneficiaire
    {
        return $this->transformer->transform($entity, ProfilBeneficiaire::class);
    }

    protected function getResourceClass(): string
    {
        return ProfilBeneficiaire::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\ProfilBeneficiaire::class;
    }

}