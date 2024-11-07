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

namespace App\State\TypeEquipement;

use ApiPlatform\State\ProviderInterface;
use App\ApiResource\TypeEquipement;
use App\State\AbstractEntityProvider;
use App\State\MappedEntityTransformer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class TypeEquipementProvider extends AbstractEntityProvider
{

    public function __construct(#[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')] ProviderInterface       $itemProvider,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')] ProviderInterface $collectionProvider,
                                private readonly MappedEntityTransformer                                                      $transformer)
    {

        parent::__construct($itemProvider, $collectionProvider);
    }

    public function transform($entity): TypeEquipement
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->transformer->transform($entity, TypeEquipement::class);
    }

    protected function getResourceClass(): string
    {
        return TypeEquipement::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\TypeEquipement::class;
    }
}