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

namespace App\State\PeriodeRH;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\PeriodeRH;
use App\Message\RessourceCollectionModifieeMessage;
use App\Message\RessourceModifieeMessage;
use App\State\TransformerService;
use Exception;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class PeriodeProcessor implements ProcessorInterface
{
    public function __construct(private PeriodeManager      $manager,
                                private TransformerService  $transformerService,
                                private MessageBusInterface $messageBus)
    {

    }

    /**
     * @param PeriodeRH $data
     * @param Operation $operation
     * @param array     $uriVariables
     * @param array     $context
     * @return mixed
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        //on n'a que POST et PATCH
        $resource = $this->transformerService->transform($this->manager->save($data), PeriodeRH::class);

        if (null !== $data->id) {
            $this->messageBus->dispatch(new RessourceModifieeMessage($resource));
        } else {
            $this->messageBus->dispatch(new RessourceCollectionModifieeMessage($resource));
        }

        return $resource;
    }

}