<?php
/*
 * Copyright (c) 2024-2026. Esup - Université de Bordeaux.
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 *  For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 *  @author Manuel Rossard <manuel.rossard@u-bordeaux.fr>
 *  @author Kevin Leveillard <kevin.leveillard@agroparistech.fr>
 *
 */

namespace App\Service\SiScol;

use InvalidArgumentException;
use LogicException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class SiScolDataProviderFactory
{

    public function __construct(
        #[Autowire(env: 'resolve:SI_SCOL')]
        private string $siScol,
        #[AutowireIterator('oasis.siscol_provider')]
        private iterable $providers
    ) {}

    /**
     * Création du provider en fonction de la variable d'environnement SI_SCOL
     *
     * @return string
     */
    public function create(): AbstractSiScolDataProvider
    {
        $providerName = ucfirst(strtolower($this->siScol));

        foreach ($this->providers as $provider) {
            if (strcasecmp($provider->getProviderId(), $this->siScol) === 0) {
                return $provider;
            }
        }

        throw new InvalidArgumentException(
            sprintf('Le provider "%s" n\'existe pas.', $this->siScol)
        );
    }
}
