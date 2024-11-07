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

namespace App\State\Evenement;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\Evenement;
use App\Message\RessourceCollectionModifieeMessage;
use App\Message\RessourceModifieeMessage;
use App\State\TransformerService;
use Exception;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class EvenementProcessor implements ProcessorInterface
{

    public function __construct(private EvenementManager    $evenementManager,
                                private TransformerService  $transformerService,
                                private ValidatorInterface  $validator,
                                private MessageBusInterface $messageBus)
    {
    }

    /**
     * @param Evenement $data
     * @param Operation $operation
     * @param array     $uriVariables
     * @param array     $context
     * @return array|mixed|object|null
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Delete) {
            $errors = $this->validator->validate(value: $data, groups: ['deleteValidation']);
            if ($errors->count() > 0) {
                throw new ConflictHttpException($errors->get(0)->getMessage());
            }
            $this->evenementManager->delete($data);
            $this->messageBus->dispatch(new RessourceCollectionModifieeMessage($data));
            return null;
        } else {
            $resource = $this->transformerService->transform($this->evenementManager->maj($data), Evenement::class);
            if (null !== $data->id) {
                $this->messageBus->dispatch(new RessourceModifieeMessage($resource));
            } else {
                $this->messageBus->dispatch(new RessourceCollectionModifieeMessage($resource));
            }
            return $resource;
        }
    }
}