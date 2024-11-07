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

namespace App\State\Question;

use ApiPlatform\State\ProviderInterface;
use App\ApiResource\OptionReponse;
use App\ApiResource\Question;
use App\State\AbstractEntityProvider;
use App\State\OptionReponse\OptionReponseProvider;
use Exception;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class QuestionProvider extends AbstractEntityProvider
{

    public function __construct(#[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')] ProviderInterface       $itemProvider,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')] ProviderInterface $collectionProvider,
                                private readonly OptionReponseProvider                                                        $optionReponseProvider)
    {
        parent::__construct($itemProvider, $collectionProvider);
    }

    #[Override] protected function getResourceClass(): string
    {
        return Question::class;
    }

    #[Override] protected function getEntityClass(): string
    {
        return \App\Entity\Question::class;
    }

    /**
     * @param \App\Entity\Question $entity
     * @return Question
     * @throws Exception
     */
    #[Override] public function transform($entity): mixed
    {
        $resource = new Question();
        $resource->id = $entity->getId();
        $resource->libelle = $entity->getLibelle();
        $resource->obligatoire = $entity->isObligatoire();
        $resource->choixMultiple = $entity->isChoixMultiple();
        $resource->aide = $entity->getAide();
        $resource->typeReponse = $entity->getTypeReponse();

        //gestion des tables de ref
        if ($entity->getTypeReponse() !== \App\Entity\Question::TYPE_TEXT) {
            $resource->optionsReponses = match (($table = $entity->getTableOptions())) {
                null => array_map(
                    fn($option) => $this->transformerService->transform($option, OptionReponse::class),
                    $entity->getOptionsReponse()->toArray()
                ),
                default => $this->optionReponseProvider->getOptionsForTable($table, $resource->id)
            };
        }

        $resource->tableOptions = $entity->getTableOptions();

        return $resource;
    }
}