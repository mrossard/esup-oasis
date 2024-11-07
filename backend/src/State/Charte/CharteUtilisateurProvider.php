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

namespace App\State\Charte;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Operation;
use App\ApiResource\CharteUtilisateur;
use App\ApiResource\Demande;
use App\Entity\CharteDemandeur;
use App\Filter\CharteUtilisateurFilter;
use App\State\AbstractEntityProvider;
use Exception;
use Override;

class CharteUtilisateurProvider extends AbstractEntityProvider
{

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof GetCollection) {
            //ajout d'un filtre sur utilisateur!
            $context['filters'][CharteUtilisateurFilter::PROPERTY] = $uriVariables['uid'];
            unset($uriVariables['uid']);

            return parent::provide($operation, $uriVariables, $context);
        }

        //on reconstruit une opération qui ne va pas exploser en vol
        $relevantVariables = ['id' => $uriVariables['id']];

        $link = new Link(parameterName: 'id', fromClass: CharteDemandeur::class, identifiers: ['id']);
        $relevantOperation = (new (get_class($operation)))->withClass(CharteDemandeur::class)
            ->withStateOptions($operation->getStateOptions())
            ->withUriVariables([$link]);

        return parent::provide(
            operation   : $relevantOperation,
            uriVariables: $relevantVariables,
            context     : $context
        );
    }

    #[Override] protected function getResourceClass(): string
    {
        return CharteUtilisateur::class;
    }

    #[Override] protected function getEntityClass(): string
    {
        return CharteDemandeur::class;
    }

    /**
     * @param CharteDemandeur $entity
     * @return CharteUtilisateur
     * @throws Exception
     */
    #[Override] public function transform($entity): mixed
    {
        $resource = new CharteUtilisateur();
        $resource->id = $entity->getId();
        $resource->uid = $entity->getDemande()->getDemandeur()->getUid();
        $resource->libelle = $entity->getLibelle();
        $resource->contenu = $entity->getContenu();
        $resource->dateValidation = $entity->getDateValidation();
        $resource->demande = $this->transformerService->transform($entity->getDemande(), Demande::class);

        return $resource;
    }
}