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

namespace App\State\EtapeDemande;

use App\ApiResource\EtapeDemande;
use App\ApiResource\Question;
use App\Entity\QuestionEtapeDemande;
use App\State\AbstractEntityProvider;
use Exception;
use Override;

class EtapeDemandeProvider extends AbstractEntityProvider
{

    #[Override] protected function getResourceClass(): string
    {
        return EtapeDemande::class;
    }

    #[Override] protected function getEntityClass(): string
    {
        return \App\Entity\EtapeDemande::class;
    }

    /**
     * @param \App\Entity\EtapeDemande $entity
     * @return EtapeDemande
     * @throws Exception
     */
    #[Override] public function transform($entity): mixed
    {
        $resource = new EtapeDemande();
        $resource->id = $entity->getId();
        $resource->libelle = $entity->getLibelle();
        $resource->ordre = $entity->getOrdre();
        $resource->questions = array_map(
            fn(QuestionEtapeDemande $question) => $this->transformerService->transform($question->getQuestion(), Question::class),
            $entity->getQuestionsEtape()->toArray()

        );
        return $resource;
    }
}