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

namespace App\State\Parametre;

use App\ApiResource\Parametre;
use App\ApiResource\ValeurParametre;
use App\State\AbstractEntityProvider;
use Exception;
use Symfony\Component\Clock\ClockAwareTrait;

class ParametreProvider extends AbstractEntityProvider
{
    use ClockAwareTrait;

    /**
     * @param \App\Entity\Parametre $entity
     * @return Parametre
     * @throws Exception
     */
    public function transform($entity): Parametre
    {
        $param = new Parametre();
        $param->cle = $entity->getCle();
        $param->fichier = $entity->isFichier();
        $param->valeurs = array_map(
            fn($val) => $this->transformerService->transform($val, ValeurParametre::class),
            $entity->getValeursParametres()->toArray()
        );

        $param->valeursCourantes = array_map(
            fn($valeur) => $this->transformerService->transform($valeur, ValeurParametre::class),
            $entity->getValeurCourante(true) ?? [],
        );

        return $param;
    }

    protected function getResourceClass(): string
    {
        return Parametre::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\Parametre::class;
    }
}