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

namespace App\Filter;

use ApiPlatform\Metadata\FilterInterface;
use ApiPlatform\Doctrine\Common\Filter\OrderFilterInterface;

class ServicesFaitsPeriodeOrderFilter implements FilterInterface
{

    public function getDescription(string $resourceClass): array
    {
        return [
            'order[debut]' => [
                'property' => 'debut',
                'type' => 'string',
                'required' => false,
                'schema' => [
                    'type' => 'string',
                    'enum' => [
                        strtolower(OrderFilterInterface::DIRECTION_ASC),
                        strtolower(OrderFilterInterface::DIRECTION_DESC),
                    ],
                ],
            ],
        ];
    }
}