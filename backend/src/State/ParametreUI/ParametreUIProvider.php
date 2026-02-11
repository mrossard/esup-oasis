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
use App\Repository\ParametreUIRepository;
use App\State\Utilisateur\UtilisateurManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class ParametreUIProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $collectionProvider,
        private ParametreUIRepository $parametreUIRepository,
        private UtilisateurManager $utilisateurManager,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof GetCollection) {
            return array_map(
                fn($param) => new ParametreUI($param),
                iterator_to_array($this->collectionProvider->provide($operation, $uriVariables, $context)),
            );
        }

        $param = $this->parametreUIRepository->findOneBy([
            'utilisateur' => $this->utilisateurManager->parUid($uriVariables['uid']),
            'cle' => $uriVariables['cle'],
        ]);

        return match ($param) {
            null => null,
            default => new ParametreUI($param),
        };
    }
}
