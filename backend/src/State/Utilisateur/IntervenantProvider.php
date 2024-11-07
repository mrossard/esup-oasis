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

namespace App\State\Utilisateur;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Utilisateur;
use App\Filter\IntervenantFilter;

readonly class IntervenantProvider implements ProviderInterface
{
    public function __construct(private UtilisateurProvider $utilisateurProvider)
    {

    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Utilisateur|array|PaginatorInterface
    {
        $context['filters'][IntervenantFilter::PROPERTY] = true;

        return $this->utilisateurProvider->provide(
            $operation,
            $uriVariables,
            $context
        );
    }
}