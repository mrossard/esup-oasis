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

use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Operation;
use App\ApiResource\Telechargement;
use App\ApiResource\ValeurParametre;
use App\State\AbstractEntityProvider;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ValeurParametreProvider extends AbstractEntityProvider
{

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $relevantVariables = ['id' => $uriVariables['id']];

        $link = new Link(parameterName: 'id', fromClass: ValeurParametre::class, identifiers: ['id']);
        $relevantOperation = (new (get_class($operation)))->withClass(ValeurParametre::class)
            ->withStateOptions($operation->getStateOptions())
            ->withUriVariables([$link]);

        $valeur = parent::provide(
            operation   : $relevantOperation,
            uriVariables: $relevantVariables,
            context     : $context
        );

        if ($valeur->cle !== $uriVariables['cle']) {
            throw new UnprocessableEntityHttpException($uriVariables['uid'] . " n'a pas de valeur d'id " . $uriVariables['id']);
        }

        return $valeur;
    }

    protected function getResourceClass(): string
    {
        return ValeurParametre::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\ValeurParametre::class;
    }

    /**
     * @param \App\Entity\ValeurParametre $entity
     * @return mixed
     */
    public function transform($entity): ValeurParametre
    {
        $resource = new ValeurParametre();
        $resource->id = $entity->getId();
        $resource->valeur = $entity->getValeur();
        $resource->fichier = $this->transformerService->transform($entity->getFichier(), Telechargement::class);
        $resource->debut = $entity->getDebut();
        $resource->fin = $entity->getFin();
        $resource->cle = $entity->getParametre()->getCle();

        return $resource;
    }
}