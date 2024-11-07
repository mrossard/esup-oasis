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

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\TransformerService;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [new Get()],
    openapi   : false
)]
class ReponseDemande
{
    public int $id;

    #[Groups([Demande::GROUP_OUT])]
    public ?string $commentaire;
    /**
     * @var OptionReponse[]
     */
    #[Groups([Demande::GROUP_OUT])]
    public array $optionsReponses;

    /**
     * @var Telechargement[]
     */
    #[Groups([Demande::GROUP_OUT])]
    public array $piecesJustificatives;

    function __construct(\App\Entity\Reponse $reponse, private readonly TransformerService $transformerService)
    {
        $this->id = $reponse->getId();
        $this->commentaire = $reponse->getCommentaire();
        $this->optionsReponses = array_map(
            fn($option) => $this->transformerService->transform($option, OptionReponse::class),
            $reponse->getOptionsChoisiesTousTypes()
        );

        $this->piecesJustificatives = array_map(
            fn($pj) => $this->transformerService->transform($pj, Telechargement::class),
            $reponse->getPiecesJustificatives()->toArray()
        );

        //on ajoute l'id de question qui manque...
        array_walk($this->optionsReponses, fn($option) => $option->questionId = $reponse->getQuestion()->getId());
    }

}