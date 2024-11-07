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

namespace App\State\CampagneDemande;

use ApiPlatform\Metadata\Exception\ItemNotFoundException;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\CampagneDemande;
use App\ApiResource\TypeDemande;
use App\Message\RessourceCollectionModifieeMessage;
use App\Message\RessourceModifieeMessage;
use App\Repository\CampagneDemandeRepository;
use App\Repository\CommissionRepository;
use App\Repository\TypeDemandeRepository;
use App\State\TransformerService;
use Override;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CampagneDemandeProcessor implements ProcessorInterface
{

    function __construct(private CampagneDemandeRepository $campagneDemandeRepository,
                         private CommissionRepository      $commissionRepository,
                         private TypeDemandeRepository     $typeDemandeRepository,
                         private TransformerService        $transformerService,
                         private MessageBusInterface       $messageBus)
    {

    }

    /**
     * @param CampagneDemande $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return CampagneDemande
     */
    #[Override] public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): CampagneDemande
    {
        if (null === $data->id) {
            $entity = new \App\Entity\CampagneDemande();
        } else {
            $entity = $this->campagneDemandeRepository->find($data->id);
        }

        if (null === ($typeDemande = $this->typeDemandeRepository->find($uriVariables['typeId']))) {
            throw new ItemNotFoundException("Type de demande inconnu : " . $uriVariables['typeId']);
        }

        $entity->setLibelle($data->libelle);
        $entity->setDebut($data->debut);
        $entity->setFin($data->fin);
        $entity->setDateCommission($data->dateCommission);
        $entity->setDateArchivage($data->dateArchivage);
        $entity->setTypeDemande($typeDemande);
        $entity->setCommission(match ($data->commission) {
            null => null,
            default => $this->commissionRepository->find($data->commission->id)
        });
        $entity->setAnneeCible($data->anneeCible);

        $this->campagneDemandeRepository->save($entity, true);

        $resource = $this->transformerService->transform($entity, CampagneDemande::class);
        $typeDemandeResource = $this->transformerService->transform($typeDemande, TypeDemande::class);

        if (null === $data->id) {
            $this->messageBus->dispatch(new RessourceCollectionModifieeMessage($resource));
        } else {
            $this->messageBus->dispatch(new RessourceModifieeMessage($resource));
        }

        $this->messageBus->dispatch(new RessourceModifieeMessage($typeDemandeResource));

        return $resource;
    }
}