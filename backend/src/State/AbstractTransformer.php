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

namespace App\State;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Contracts\Service\Attribute\Required;

#[AutoconfigureTag('transformateur')]
abstract class AbstractTransformer
{
    protected TransformerService $transformerService;

    #[Required]
    public function setTransformerService(TransformerService $transformerService): void
    {
        $this->transformerService = $transformerService;
        $this->registerTransformations();
    }

    abstract protected function registerTransformations(): void;

    /**
     * @param $entity
     * @return mixed
     */
    abstract public function transform($entity): mixed;
}