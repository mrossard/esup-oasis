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
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Entity\Beneficiaire;
use App\State\BeneficiaireProfil\BeneficiaireProfilProcessor;
use App\State\BeneficiaireProfil\BeneficiaireProfilProvider;
use App\Validator\BeneficiaireDifferentGestionnaireContraint;
use App\Validator\BeneficiaireSupprimableConstraint;
use App\Validator\ProfilAvecTypologieConstraint;
use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ApiResource(
    operations            : [
        new Get(
            uriTemplate : self::ITEM_URI,
            uriVariables: ['uid', 'id']
        ),
        new Patch(
            uriTemplate      : self::ITEM_URI,
            uriVariables     : ['uid', 'id'],
            security         : "is_granted('" . self::VOIR_PROFILS . "')",
            validationContext: ['groups' => [self::GROUP_VALIDATION_IN]],
        ),
        new Post(
            uriTemplate      : self::COLLECTION_URI,
            uriVariables     : ['uid'],
            security         : "is_granted('" . self::VOIR_PROFILS . "')",
            validationContext: ['groups' => [self::GROUP_VALIDATION_IN]],
            read             : false
        ),
        new Delete(
            uriTemplate      : self::ITEM_URI,
            uriVariables     : ['uid', 'id'],
            security         : "is_granted('" . self::VOIR_PROFILS . "')",
            validationContext: ['groups' => [self::GROUP_VALIDATION_DELETE]]
        ),
    ],
    normalizationContext  : ['groups' => [self::GROUP_OUT]],
    denormalizationContext: ['groups' => [self::GROUP_IN]],
    openapi               : new Operation(tags: ['Utilisateurs']),
    order                 : ['debut' => 'DESC '],
    security              : "is_granted('ROLE_PLANIFICATEUR')",
    provider              : BeneficiaireProfilProvider::class,
    processor             : BeneficiaireProfilProcessor::class,
    stateOptions          : new Options(entityClass: Beneficiaire::class)
)]
#[ApiFilter(SearchFilter::class, properties: ['profil'])]
#[ProfilAvecTypologieConstraint(groups: [self::GROUP_VALIDATION_IN])]
#[BeneficiaireDifferentGestionnaireContraint(groups: [self::GROUP_VALIDATION_IN])]
#[BeneficiaireSupprimableConstraint(groups: [self::GROUP_VALIDATION_DELETE])]
final class BeneficiaireProfil
{
    public const string COLLECTION_URI = '/utilisateurs/{uid}/profils';
    public const string ITEM_URI = '/utilisateurs/{uid}/profils/{id}';

    public const string GROUP_OUT = 'beneficiaires_profils:out';
    public const string GROUP_IN = 'beneficiaires_profils:in';
    public const string GROUP_VALIDATION_DELETE = 'beneficiaires_profils:validation:delete';
    public const string GROUP_VALIDATION_IN = 'beneficiaires_profils:validation:in';
    public const string VOIR_PROFILS = 'VOIR_PROFILS';

    #[ApiProperty(identifier: true)]
    #[Groups([self::GROUP_OUT])]
    public ?int $id = null;

    //Copie de l'UID utilisateur. Non présenté à l'extérieur, juste utile pour uriVariables
    public ?string $uid;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    #[ApiProperty(security: "is_granted('" . self::VOIR_PROFILS . "')")]
    public ?ProfilBeneficiaire $profil = null;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    #[Assert\NotBlank(groups: [self::GROUP_VALIDATION_IN])]
    public DateTimeInterface $debut;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public ?DateTimeInterface $fin = null;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    #[Assert\NotBlank(groups: [self::GROUP_VALIDATION_IN])]
    public Utilisateur $gestionnaire;

    /**
     * @var TypologieHandicap[]
     */
    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    #[Assert\All([new Assert\Type(TypologieHandicap::class)], groups: [self::GROUP_VALIDATION_IN])]
    #[ApiProperty(security: "is_granted('" . self::VOIR_PROFILS . "')")]
    public array $typologies;

    #[Groups([self::GROUP_OUT, self::GROUP_IN])]
    public bool $avecAccompagnement = true;
}