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


use ApiPlatform\Metadata\ApiResource;
use AutoMapper\AutoMapper;
use ReflectionClass;

readonly class MappedEntityTransformer
{
    public function transform($data, $to, $groups = []): object|array|null
    {
        if (empty($groups)) {
            //on tente de récupérer le groupe de normalization ou denormalization qui va bien
            $reflectionClass = new ReflectionClass($data);
            $attributes = $reflectionClass->getAttributes(ApiResource::class);

            if (!empty($attributes)) {
                // ressource -> entité
                $resourceClass = get_class($data);
                if (defined($resourceClass . '::GROUP_IN')) {
                    $groups = [constant($resourceClass . '::GROUP_IN')];
                }
            } else {
                // entité -> resource
                $resourceClass = is_string($to) ? $to : get_class($to);
                if (defined($resourceClass . '::GROUP_OUT')) {
                    $groups = [constant($resourceClass . '::GROUP_OUT')];
                }
            }
        }
        return AutoMapper::create()->map($data, $to, ['groups' => $groups,]);
    }

}