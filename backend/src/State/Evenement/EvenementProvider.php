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

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Evenement;
use App\ApiResource\Utilisateur;
use App\Entity\ApplicationCliente;
use App\Entity\Service;
use App\State\MappedCollectionPaginator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

readonly class EvenementProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $collectionProvider,
        private Security $security,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $utilisateur = $this->security->getUser();

        if ($operation instanceof GetCollection) {
            //on ajoute "de force" un filtre sur l'utilisateur courant
            if (!$this->security->isGranted(ApplicationCliente::ROLE_APPLICATION_CLIENTE)) {
                $user = $utilisateur;
                $context['filters']['utilisateurConcerne'] =
                    Utilisateur::COLLECTION_URI . '/' . $user->getUserIdentifier();
            }
        }

        if ($operation instanceof GetCollection) {
            $result = $this->collectionProvider->provide($operation, $uriVariables, $context);
            if (
                !array_key_exists('filters', $context)
                || !array_key_exists('debut', $context['filters'])
                || !array_key_exists('fin', $context['filters'])
            ) {
                //noop
            } else {
                $start = new DatePoint(current($context['filters']['debut']));
                $end = new DatePoint(current($context['filters']['fin']));
                while ($start <= $end) {
                    $start = $start->modify('+1 day');
                }
            }
            assert($result instanceof PaginatorInterface);
            return new MappedCollectionPaginator($result, fn($utilisateur) => new Evenement($utilisateur));
        }

        $result = $this->itemProvider->provide($operation, $uriVariables, $context);

        return new Evenement($result);
    }
}
