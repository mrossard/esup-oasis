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

namespace App\State\Competence;

use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Competence;
use App\State\AbstractEntityProvider;
use App\State\MappedEntityTransformer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CompetenceProvider extends AbstractEntityProvider
{

    public function __construct(#[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')] ProviderInterface       $itemProvider,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')] ProviderInterface $collectionProvider,
                                private readonly MappedEntityTransformer                                                      $transformer)
    {

        parent::__construct($itemProvider, $collectionProvider);
    }

    public function transform($entity): mixed
    {
        return $this->transformer->transform($entity, Competence::class);
    }


    protected function getResourceClass(): string
    {
        return Competence::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\Competence::class;
    }
}