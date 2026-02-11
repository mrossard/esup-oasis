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
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Evenement;
use App\ApiResource\Utilisateur;
use App\Entity\ApplicationCliente;
use App\Entity\Service;
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
        private TagAwareCacheInterface $cache,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $utilisateur = $this->security->getUser();

        if ($operation instanceof GetCollection) {
            //calcul de la clé pour mise en cache
            $cacheKey = match (true) {
                $this->security->isGranted(\App\Entity\Utilisateur::ROLE_PLANIFICATEUR)
                    => 'evenements_role_planificateur_',
                default => 'evenements_user_' . $utilisateur->getUserIdentifier() . '_',
            };
            //Evenements à valider : différences en fonction du service de l'utilisateur!
            if (array_key_exists('filters', $context) && array_key_exists('aValider', $context['filters'])) {
                assert($utilisateur instanceof \App\Entity\Utilisateur);
                $cacheKey .= array_reduce(
                    array: $utilisateur->getServices()->toArray(),
                    callback: function ($carry, Service $item) {
                        return $carry . '_' . $item->getId();
                    },
                    initial: '',
                );
            }

            //on colle tous les filtres
            $cacheKey .= urlencode(json_encode($context['filters'] ?? []));

            //on ajoute "de force" un filtre sur l'utilisateur courant
            if (!$this->security->isGranted(ApplicationCliente::ROLE_APPLICATION_CLIENTE)) {
                $user = $utilisateur;
                $context['filters']['utilisateurConcerne'] =
                    Utilisateur::COLLECTION_URI . '/' . $user->getUserIdentifier();
            }
        } else {
            $cacheKey = 'evenement_' . $uriVariables['id'];
        }

        return $this->cache->get(key: $cacheKey, callback: function (ItemInterface $item) use (
            $operation,
            $uriVariables,
            $context,
        ) {
            $item->expiresAfter(7200);

            if ($operation instanceof GetCollection) {
                $result = $this->collectionProvider->provide($operation, $uriVariables, $context);
                if (
                    !array_key_exists('filters', $context)
                    || !array_key_exists('debut', $context['filters'])
                    || !array_key_exists('fin', $context['filters'])
                ) {
                    $item->tag('collection_evenements_sans_dates');
                } else {
                    $start = new DatePoint(current($context['filters']['debut']));
                    $end = new DatePoint(current($context['filters']['fin']));
                    while ($start <= $end) {
                        $item->tag('collection_evenements_' . $start->format('Y-m-d'));
                        $start = $start->modify('+1 day');
                    }
                }
                foreach ($result as $res) {
                    $item->tag('evenement_' . $res->getId());
                }
                return array_map(fn($event) => new Evenement($event), iterator_to_array($result)); //todo: pagination??
            }

            $result = $this->itemProvider->provide($operation, $uriVariables, $context);
            $item->tag('evenement_' . $result->getId());

            return new Evenement($result);
        });
    }
}
