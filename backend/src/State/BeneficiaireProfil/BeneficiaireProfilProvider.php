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

namespace App\State\BeneficiaireProfil;

use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\BeneficiaireProfil;
use App\ApiResource\ProfilBeneficiaire;
use App\ApiResource\TypologieHandicap;
use App\ApiResource\Utilisateur;
use App\Entity\Beneficiaire;
use App\State\AbstractEntityProvider;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class BeneficiaireProfilProvider extends AbstractEntityProvider
{

    public function __construct(#[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')] ProviderInterface       $itemProvider,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')] ProviderInterface $collectionProvider)
    {
        parent::__construct($itemProvider, $collectionProvider);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $relevantVariables = ['id' => $uriVariables['id']];

        $link = new Link(parameterName: 'id', fromClass: BeneficiaireProfil::class, identifiers: ['id']);
        $relevantOperation = (new (get_class($operation)))->withClass(BeneficiaireProfil::class)
            ->withStateOptions($operation->getStateOptions())
            ->withUriVariables([$link]);

        $benef = parent::provide(
            operation   : $relevantOperation,
            uriVariables: $relevantVariables,
            context     : $context
        );

        if ($benef->uid !== $uriVariables['uid']) {
            throw new UnprocessableEntityHttpException($uriVariables['uid'] . " n'a pas de beneficiaire d'id " . $uriVariables['id']);
        }

        return $benef;
    }

    /**
     * @param Beneficiaire $entity
     * @return BeneficiaireProfil
     * @throws Exception
     */
    public function transform($entity): BeneficiaireProfil
    {
        $bp = new BeneficiaireProfil();
        $bp->id = $entity->getId();
        $bp->uid = $entity->getUtilisateur()->getUid();
        $bp->debut = $entity->getDebut();
        $bp->fin = $entity->getFin();
        $bp->profil = $this->transformerService->transform($entity->getProfil(), ProfilBeneficiaire::class);
        $bp->gestionnaire = $this->transformerService->transform($entity->getGestionnaire(), Utilisateur::class);
        $bp->typologies = array_map(
            callback: fn($typo) => $this->transformerService->transform($typo, TypologieHandicap::class),
            array   : $entity->getTypologies()->toArray()
        );
        $bp->avecAccompagnement = $entity->isAvecAccompagnement();
        return $bp;
    }

    protected function getResourceClass(): string
    {
        return BeneficiaireProfil::class;
    }

    protected function getEntityClass(): string
    {
        return Beneficiaire::class;
    }
}