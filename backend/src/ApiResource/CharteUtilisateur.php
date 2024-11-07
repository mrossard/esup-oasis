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

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\OpenApi\Model\Operation;
use App\Entity\CharteDemandeur;
use App\Filter\CharteUtilisateurFilter;
use App\State\Charte\CharteUtilisateurProcessor;
use App\State\Charte\CharteUtilisateurProvider;
use DateTimeInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ApiResource(
    operations  : [
        new GetCollection(
            uriTemplate : self::COLLECTION_URI,
            uriVariables: ['uid']
        ),
        new Get(
            uriTemplate : self::ITEM_URI,
            uriVariables: ['uid', 'id']
        ),
        new Patch(
            uriTemplate            : self::ITEM_URI,
            uriVariables           : ['uid', 'id'],
            denormalizationContext : ['groups' => [self::GROUP_IN]],
            securityPostDenormalize: "is_granted('ROLE_ADMIN') or object.uid == user.getUid()"
        ),
    ],
    openapi     : new Operation(tags: ['Utilisateurs']),
    provider    : CharteUtilisateurProvider::class,
    processor   : CharteUtilisateurProcessor::class,
    stateOptions: new Options(entityClass: CharteDemandeur::class)
)]
#[ApiFilter(CharteUtilisateurFilter::class)]
class CharteUtilisateur
{
    public const string COLLECTION_URI = '/utilisateurs/{uid}/chartes';
    public const string ITEM_URI = '/utilisateurs/{uid}/chartes/{id}';
    public const string GROUP_IN = 'charte_utilisateur:in';

    #[Ignore] public string $uid;
    #[Ignore] public ?int $id;

    public string $libelle;
    public string $contenu;

    public Demande $demande;

    #[Groups([self::GROUP_IN])]
    public ?DateTimeInterface $dateValidation = null;
}