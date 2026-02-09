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
use App\ApiResource\Campus;
use App\ApiResource\Evenement;
use App\ApiResource\TypeEquipement;
use App\ApiResource\TypeEvenement;
use App\ApiResource\Utilisateur;
use App\Entity\ApplicationCliente;
use App\Entity\Service;
use App\State\AbstractEntityProvider;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class EvenementProvider extends AbstractEntityProvider
{
    public function __construct(#[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')] ProviderInterface       $itemProvider,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')] ProviderInterface $collectionProvider,
                                private readonly Security                                                                     $security, private readonly TagAwareCacheInterface $cache)
    {
        parent::__construct($itemProvider, $collectionProvider);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $utilisateur = $this->security->getUser();

        if ($operation instanceof GetCollection) {
            //calcul de la clé pour mise en cache
            $cacheKey = match (true) {
                $this->security->isGranted(\App\Entity\Utilisateur::ROLE_PLANIFICATEUR) => 'evenements_role_planificateur_',
                default => 'evenements_user_' . $utilisateur->getUserIdentifier() . '_'
            };
            //Evenements à valider : différences en fonction du service de l'utilisateur!
            if (array_key_exists('filters', $context) && array_key_exists('aValider', $context['filters'])) {
                assert($utilisateur instanceof \App\Entity\Utilisateur);
                $cacheKey .= array_reduce(
                    array: $utilisateur->getServices()->toArray(),
                    callback: function ($carry, Service $item) {
                        return $carry . '_' . $item->getId();
                    },
                    initial: ''
                );
            }

            //on colle tous les filtres
            $cacheKey .= urlencode(json_encode($context['filters'] ?? []));

            //on ajoute "de force" un filtre sur l'utilisateur courant
            if (!$this->security->isGranted(ApplicationCliente::ROLE_APPLICATION_CLIENTE)) {
                $user = $utilisateur;
                $context['filters']['utilisateurConcerne'] = Utilisateur::COLLECTION_URI . '/' . $user->getUserIdentifier();
            }
        } else {
            $cacheKey = 'evenement_' . $uriVariables['id'];
        }

        return $this->cache->get(
            key: $cacheKey,
            callback: function (ItemInterface $item) use ($operation, $uriVariables, $context) {
                $result = parent::provide($operation, $uriVariables, $context);
                $item->expiresAfter(7200);
                if ($result instanceof Evenement) {
                    $item->tag('evenement_' . $result->id);
                } else {
                    //Collection - on tente de gérer les dates pour invalidation lors de créations
                    if (!array_key_exists('filters', $context) || !array_key_exists('debut', $context['filters'])
                        || !array_key_exists('fin', $context['filters'])) {
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
                        $item->tag('evenement_' . $res->id);
                    }
                }

                return $result;
            }
        );


    }

    public function transform($entity): mixed
    {
        return new Evenement($entity);
    }

    protected function getResourceClass(): string
    {
        return Evenement::class;
    }

    protected function getEntityClass(): string
    {
        return \App\Entity\Evenement::class;
    }
}