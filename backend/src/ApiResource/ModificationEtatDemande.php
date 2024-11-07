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

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Demande\ModificationEtatDemandeProvider;
use DateTimeInterface;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ApiResource(
    operations  : [
        new GetCollection(
            uriTemplate : self::COLLECTION_URI,
            uriVariables: [
                'demandeId' => new Link(fromProperty: 'id', toProperty: 'demande', fromClass: Demande::class),
            ]
        ),
        new Get(
            uriTemplate : self::ITEM_URI,
            uriVariables: [
                'demandeId' => new Link(fromProperty: 'id', toProperty: 'demande', fromClass: Demande::class),
                'id',
            ]
        ),
    ],
    openapi     : new Operation(tags: ['Demandes']),
    provider    : ModificationEtatDemandeProvider::class,
    stateOptions: new Options(entityClass: \App\Entity\ModificationEtatDemande::class)
)]
#[ApiFilter(OrderFilter::class, properties: ['dateModification', 'id'])]
#[ApiFilter(DateFilter::class, properties: ['dateModification'])]
#[ApiFilter(SearchFilter::class, properties: ['demande'])]
class ModificationEtatDemande
{
    public const string COLLECTION_URI = '/demandes/{demandeId}/modifications';
    public const string ITEM_URI = '/demandes/{demandeId}/modifications/{id}';

    #[Ignore] public ?int $id = null;
    public Demande $demande;

    public EtatDemande $etat;
    public EtatDemande $etatPrecedent;
    public Utilisateur $utilisateurModification;
    public ?ProfilBeneficiaire $profil = null;
    public ?string $commentaire;

    public ?DateTimeInterface $dateModification;
}