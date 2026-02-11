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

namespace App\State\Amenagement;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Composante;
use App\ApiResource\Inscription;
use App\ApiResource\Utilisateur;
use App\Entity\Amenagement;
use App\Filter\BeneficiaireAvecAmenagementEnCoursFilter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Clock\ClockAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AmenagementsParUtilisateurProvider implements ProviderInterface
{
    use ClockAwareTrait;

    public function __construct(
        private readonly Security $security,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface $collectionProvider,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $context['filters'][BeneficiaireAvecAmenagementEnCoursFilter::PROPERTY] = true;

        // les renforts ne voient que les aménagements d'aide humaine !
        if ($this->security->isGranted(\App\Entity\Utilisateur::ROLE_RENFORT)) {
            $context['filters']['examens'] = false;
            $context['filters']['pedagogique'] = false;
        }

        if ($this->security->isGranted(\App\Entity\Utilisateur::ROLE_REFERENT_COMPOSANTE)) {
            /**
             * @var \App\Entity\Utilisateur $user
             */
            $user = $this->security->getUser();

            //todo: petit trou ici : si composante fournie, vérifier qu'elle fait partie des composantes autorisées...
            if (!array_key_exists('composante', $context['filters'] ?? [])) {
                $context['filters']['composante'] = array_map(
                    fn($cmp) => Composante::COLLECTION_URI . '/' . $cmp->getId(),
                    $user->getComposantes()->toArray(),
                );
            }
        }

        /**
         * TODO: filtrer les données qui remontent avec les règles de transform()
         */
        $paginator = $this->collectionProvider->provide($operation, $uriVariables, $context);
        return $paginator;

        //        return array_map(fn($resource) => $this->transform($resource), $resources);
    }

    public function transform(Utilisateur $resource): Utilisateur
    {
        if ($this->security->isGranted(\App\Entity\Utilisateur::ROLE_RENFORT)) {
            //on ne montre que les aides humaines
            $resource->amenagements = array_values(array_filter(
                $resource->amenagements,
                fn(Amenagement $amenagement) => $amenagement->typeAmenagement->aideHumaine,
            ));
        }

        if ($this->security->isGranted(\App\Entity\Utilisateur::ROLE_REFERENT_COMPOSANTE)) {
            //on ne montre que les aménagements d'examen et pédagogiques
            $resource->amenagements = array_values(array_filter(
                $resource->amenagements,
                fn(Amenagement $amenagement) => (
                    $amenagement->typeAmenagement->examens || $amenagement->getType()->isPedagogique()
                ),
            ));
        }

        $resource->amenagements = array_values(array_map(function (Amenagement $amenagement) {
            return new \App\ApiResource\Amenagement($amenagement);
        }, $resource->amenagements));

        //on ne veut que la dernière inscription
        $inscriptions = $resource->inscriptions;
        usort($inscriptions, function (Inscription $a, Inscription $b) {
            return $b->debut <=> $a->debut;
        });
        $resource->inscriptions = empty($inscriptions) ? [] : [array_shift($inscriptions)];

        /***
         *TODO : ce qui est commenté en-dessous
         */
        //Ajouts infos manquantes pour export
        //        $resource->etatAvisEse = $entity->getEtatAvisEse();
        //
        //        $resource->tags = array_values(array_unique(
        //            array_map(
        //                fn($tag) => new Tag($tag),
        //                array_reduce(
        //                    $entity->getBeneficiairesActifs(),
        //                    fn(array $carry, Beneficiaire $beneficiaire) => [...$carry, ...$beneficiaire->getTags()],
        //                    [],
        //                ),
        //            ),
        //            SORT_REGULAR,
        //        ));

        return $resource;
    }

    public function registerTransformations(): void {}
}
