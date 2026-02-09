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

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Filter\AmenagementBeneficiaireActifFilter;
use App\Filter\AmenagementUtilisateurFilter;
use App\Filter\BeneficiaireActifTagFilter;
use App\Filter\CaseInsensitiveSearchFilter;
use App\Filter\ComposanteFormationFilter;
use App\Filter\NestedFieldSearchFilter;
use App\State\Amenagement\AmenagementProcessor;
use App\State\Amenagement\AmenagementProvider;
use App\State\Amenagement\AmenagementSansFiltreProvider;
use App\Validator\AmenagementDatesConstraint;
use App\Validator\UtilisateurBeneficiaireAmenagementConstraint;
use DateTimeInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: self::COLLECTION_URI,
            uriVariables: ['uid'],
            security: "is_granted('" . self::VOIR_AMENAGEMENTS_UTILISATEUR . "', request.attributes.get('uid'))",
        ),
        new GetCollection(
            uriTemplate: '/amenagements',
            security: "is_granted('" . self::VOIR_AMENAGEMENTS . "')",
            provider: AmenagementSansFiltreProvider::class,
        ),
        new Post(uriTemplate: self::COLLECTION_URI, uriVariables: ['uid'], read: false, map: false),
        new Get(
            uriTemplate: self::ITEM_URI,
            uriVariables: ['uid', 'id'],
            security: "is_granted('" . self::VOIR_AMENAGEMENTS_UTILISATEUR . "', request.attributes.get('uid'))",
        ),
        new Patch(
            uriTemplate: self::ITEM_URI,
            uriVariables: ['uid', 'id'],
            security: "is_granted('" . self::MODIFIER_AMENAGEMENTS_UTILISATEUR . "', request.attributes.get('uid'))",
        ),
        new Delete(
            uriTemplate: self::ITEM_URI,
            uriVariables: ['uid', 'id'],
            security: "is_granted('" . self::MODIFIER_AMENAGEMENTS_UTILISATEUR . "', request.attributes.get('uid'))",
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_OUT]],
    denormalizationContext: ['groups' => [self::GROUP_IN]],
    openapi: new Operation(tags: ['Utilisateurs']),
    provider: AmenagementProvider::class,
    processor: AmenagementProcessor::class,
    stateOptions: new Options(entityClass: \App\Entity\Amenagement::class),
)]
#[UtilisateurBeneficiaireAmenagementConstraint]
#[ApiFilter(AmenagementUtilisateurFilter::class)]
#[ApiFilter(AmenagementBeneficiaireActifFilter::class)]
#[ApiFilter(SearchFilter::class, properties: [
    'type',
    'type.categorie',
    'suivi',
])]
#[ApiFilter(CaseInsensitiveSearchFilter::class, properties: [
    'nom' => [
        'type' => 'string',
        'mapping' => 'beneficiaires.utilisateur.nom',
        'desc' => 'Nom du bénéficiaire',
    ],
])]
#[ApiFilter(BooleanFilter::class, properties: [
    'type.pedagogique',
    'type.examens',
    'type.aideHumaine',
])]
#[ApiFilter(ComposanteFormationFilter::class, properties: ['composante', 'formation'])]
#[ApiFilter(BeneficiaireActifTagFilter::class, properties: ['tags'])]
#[ApiFilter(
    OrderFilter::class,
    properties: ['beneficiaires.utilisateur.nom'],
    arguments: ['orderParameterName' => 'order'],
)]
#[ApiFilter(NestedFieldSearchFilter::class, properties: [
    'gestionnaire' => [
        'type' => 'string',
        'mapping' => 'beneficiaires.gestionnaire',
        'desc' => "gestionnaire du bénéficiaire de l'aménagement",
    ],
])]
#[AmenagementDatesConstraint]
//#[Map(target: \App\Entity\Amenagement::class, transform: [self::class, 'toEntity'])]
class Amenagement
{
    public const string COLLECTION_URI = '/utilisateurs/{uid}/amenagements';
    public const string ITEM_URI = '/utilisateurs/{uid}/amenagements/{id}';
    public const string GROUP_OUT = 'amenagement:out';
    public const string GROUP_IN = 'amenagement:in';
    public const string VOIR_AMENAGEMENTS = 'VOIR_AMENAGEMENTS';
    public const string VOIR_AMENAGEMENTS_UTILISATEUR = 'VOIR_AMENAGEMENTS_UTILISATEUR';
    public const string MODIFIER_AMENAGEMENTS_UTILISATEUR = 'MODIFIER_AMENAGEMENTS_UTILISATEUR';

    //pour uriVariables uniquement
    #[Groups([Utilisateur::AMENAGEMENTS_UTILISATEURS_OUT])]
    public ?int $id = null {
        get {
            if ($this->id === null && $this->entity !== null) {
                $this->id = $this->entity->getId();
            }
            return $this->id ?? null;
        }
    }

    #[Ignore]
    public ?string $uid = null {
        get {
            if (!isset($this->uid) && $this->entity !== null) {
                // Logic from Provider
                $beneficiaires = $this->entity->getBeneficiaires();
                if ($beneficiaires->count() > 0) {
                    $firstBenef = $beneficiaires->current();
                    if ($firstBenef) {
                        $this->uid = $firstBenef->getUtilisateur()->getUid();
                    }
                }
            }
            return $this->uid ?? null;
        }
    }

    #[Groups([self::GROUP_OUT])]
    public Utilisateur $beneficiaire {
        get {
            if (!isset($this->beneficiaire) && $this->entity !== null) {
                $beneficiaires = $this->entity->getBeneficiaires();
                if ($beneficiaires->count() > 0) {
                    $firstBenef = $beneficiaires->current();
                    if ($firstBenef) {
                        $user = $firstBenef->getUtilisateur();
                        $this->beneficiaire = new Utilisateur($user);

                        $derniereInscription = $user->getDerniereInscription();
                        if ($derniereInscription) {
                            $this->beneficiaire->inscriptions = [new Inscription($derniereInscription)];
                        } else {
                            $this->beneficiaire->inscriptions = [];
                        }
                    }
                }
            }
            return $this->beneficiaire;
        }
    }

    #[Groups([self::GROUP_OUT, self::GROUP_IN, Utilisateur::AMENAGEMENTS_UTILISATEURS_OUT])]
    #[Assert\NotNull]
    public TypeAmenagement $typeAmenagement {
        get {
            if (!isset($this->typeAmenagement) && $this->entity !== null && $this->entity->getType()) {
                $this->typeAmenagement = new TypeAmenagement($this->entity->getType());
            }
            return $this->typeAmenagement;
        }
    }

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public bool $semestre1 = false {
        get {
            if ($this->entity !== null) {
                return $this->entity->isSemestre1() ?? false;
            }
            return $this->semestre1;
        }
    }
    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public bool $semestre2 = false {
        get {
            if ($this->entity !== null) {
                return $this->entity->isSemestre2() ?? false;
            }
            return $this->semestre2;
        }
    }

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public ?DateTimeInterface $debut = null {
        get {
            if (!isset($this->debut) && $this->entity !== null) {
                $this->debut = $this->entity->getDebut();
            }
            return $this->debut ?? null;
        }
    }
    #[Assert\GreaterThan(propertyPath: 'debut', message: 'fin doit être postérieur à debut')]
    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public ?DateTimeInterface $fin = null {
        get {
            if (!isset($this->fin) && $this->entity !== null) {
                $this->fin = $this->entity->getFin();
            }
            return $this->fin ?? null;
        }
    }

    #[Groups([self::GROUP_OUT, self::GROUP_IN, Utilisateur::AMENAGEMENTS_UTILISATEURS_OUT])]
    public ?string $commentaire = null {
        get {
            if (!isset($this->commentaire) && $this->entity !== null) {
                $this->commentaire = $this->entity->getCommentaire();
            }
            return $this->commentaire ?? null;
        }
    }

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public ?TypeSuiviAmenagement $suivi = null {
        get {
            if (!isset($this->suivi) && $this->entity !== null && $this->entity->getSuivi()) {
                $this->suivi = new TypeSuiviAmenagement($this->entity->getSuivi());
            }
            return $this->suivi ?? null;
        }
    }

    public function __construct(
        private readonly ?\App\Entity\Amenagement $entity = null,
    ) {}
}
