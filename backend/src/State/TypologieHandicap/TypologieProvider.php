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

namespace App\State\TypologieHandicap;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\TypologieHandicap;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class TypologieProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $collectionProvider,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof GetCollection) {
            return array_map(
                fn($avis) => new TypologieHandicap($avis),
                iterator_to_array($this->collectionProvider->provide($operation, $uriVariables, $context)),
            );
        }
        $entity = $this->itemProvider->provide($operation, $uriVariables, $context);
        return match ($entity) {
            null => null,
            default => new TypologieHandicap($entity),
        };
    }
}
