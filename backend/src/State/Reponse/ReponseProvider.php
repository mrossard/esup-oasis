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

namespace App\State\Reponse;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Demande;
use App\ApiResource\Telechargement;
use App\ApiResource\Question;
use App\ApiResource\Reponse;
use App\ApiResource\Utilisateur;
use App\Entity\Fichier;
use App\Repository\DemandeRepository;
use App\State\AbstractEntityProvider;
use App\State\OptionReponse\OptionReponseProvider;
use Exception;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ReponseProvider extends AbstractEntityProvider
{

    public function __construct(#[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')] ProviderInterface       $itemProvider,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')] ProviderInterface $collectionProvider,
                                private readonly DemandeRepository                                                            $demandeRepository,
                                private readonly OptionReponseProvider                                                        $optionReponseProvider)
    {
        parent::__construct($itemProvider, $collectionProvider);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        //input url : {demandeId} + {questionId}
        $demande = $this->demandeRepository->find($uriVariables['demandeId']);

        $reponses = array_filter(
            $demande->getDemandeur()->getReponses()->toArray(),
            fn(Reponse $reponse) => $reponse->getCampagne() === $demande->getCampagne() && $reponse->getQuestion()->getId() === $uriVariables['questionId']
        );

        return match (true) {
            empty($reponses) => null,
            default => current($reponses)
        };
    }


    #[Override] protected function getResourceClass(): string
    {
        return Reponse::class;
    }

    #[Override] protected function getEntityClass(): string
    {
        return \App\Entity\Reponse::class;
    }

    /**
     * @param \App\Entity\Reponse $entity
     * @return mixed
     * @throws Exception
     */
    #[Override] public function transform($entity): mixed
    {
        $resource = new Reponse();
        $resource->repondant = $this->transformerService->transform($entity->getRepondant(), Utilisateur::class);
        $resource->question = $this->transformerService->transform($entity->getQuestion(), Question::class);
        $resource->questionId = $resource->question->id;

        $resource->optionsChoisies = $this->optionReponseProvider->getOptionsForReponse($entity);
        $resource->demande = $this->transformerService->transform(
            $entity->getRepondant()->getDemandePourCampagne($entity->getCampagne()),
            Demande::class
        );
        $resource->demandeId = $resource->demande->id;
        $resource->commentaire = $entity->getCommentaire();
        $resource->piecesJustificatives = array_map(
            fn(Fichier $fichier) => $this->transformerService->transform($fichier, Telechargement::class),
            $entity->getPiecesJustificatives()->toArray()
        );
        $resource->dateModification = $entity->getDateModification();

        return $resource;
    }
}