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

namespace App\State\PieceJointeBeneficiaire;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Operation;
use App\ApiResource\PieceJointeBeneficiaire;
use App\ApiResource\Telechargement;
use App\ApiResource\Utilisateur;
use App\Filter\PieceJointeBeneficiaireUidFilter;
use App\State\AbstractEntityProvider;
use Exception;

class PieceJointeBeneficiaireProvider extends AbstractEntityProvider
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof GetCollection) {
            //ajout d'un filtre sur utilisateur!
            $context['filters'][PieceJointeBeneficiaireUidFilter::PROPERTY] = $uriVariables['uid'];
            unset($uriVariables['uid']);

            return parent::provide($operation, $uriVariables, $context);
        }

        //on reconstruit une opération qui ne va pas exploser en vol
        $relevantVariables = ['id' => $uriVariables['id']];

        $link = new Link(parameterName: 'id', fromClass: PieceJointeBeneficiaire::class, identifiers: ['id']);
        $relevantOperation = (new (get_class($operation)))->withClass(PieceJointeBeneficiaire::class)
            ->withStateOptions($operation->getStateOptions())
            ->withUriVariables([$link]);

        return parent::provide(
            operation   : $relevantOperation,
            uriVariables: $relevantVariables,
            context     : $context
        );
    }


    protected function getResourceClass(): string
    {
        return PieceJointeBeneficiaire::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\PieceJointeBeneficiaire::class;
    }

    /**
     * @param \App\Entity\PieceJointeBeneficiaire $entity
     * @return PieceJointeBeneficiaire
     * @throws Exception
     */
    public function transform($entity): mixed
    {
        $resource = new PieceJointeBeneficiaire();
        $resource->id = $entity->getId();
        $resource->uid = $entity->getBeneficiaire()->getUid();
        $resource->libelle = $entity->getLibelle();
        $resource->utilisateurCreation = $this->transformerService->transform($entity->getUtilisateurCreation(), Utilisateur::class);
        $resource->dateDepot = $entity->getDateDepot();
        $resource->fichier = $this->transformerService->transform($entity->getFichier(), Telechargement::class);

        return $resource;
    }
}