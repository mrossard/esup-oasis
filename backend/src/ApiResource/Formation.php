<?php

/*
 * Copyright (c) 2024-2026. Esup - Université de Bordeaux.
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 *  For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 *  @author Manuel Rossard <manuel.rossard@u-bordeaux.fr>
 *
 */

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\OpenApi\Model\Operation;
use App\Filter\CaseInsensitiveOrderFilter;
use App\Filter\InscriptionEnCoursFilter;
use App\Filter\ResourceIriFilter;
use ReflectionProperty;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(uriTemplate: self::ITEM_URI, uriVariables: ['id']),
        new GetCollection(uriTemplate: self::COLLECTION_URI, parameters: [
            'composante' => new QueryParameter(filter: ResourceIriFilter::class, property: 'composante'),
            'order[:property]' => new QueryParameter(filter: new CaseInsensitiveOrderFilter(), properties: ['libelle']),
            'avecInscriptions' => new QueryParameter(
                filter: new InscriptionEnCoursFilter(),
                property: 'avecInscriptions',
            ),
        ]),
    ],
    openapi: new Operation(tags: ['Referentiel']),
    stateOptions: new Options(entityClass: \App\Entity\Formation::class),
)]
final class Formation
{
    public const string COLLECTION_URI = '/formations';
    public const string ITEM_URI = self::COLLECTION_URI . '/{id}';

    #[ApiProperty(identifier: true)]
    public int $id { //pas nullable, vient toujours de la base
        get {
            $prop = new ReflectionProperty(self::class, 'id');
            if (!$prop->isInitialized($this) && $this->entity !== null) {
                $this->id = $this->entity->getId();
            }
            return $this->id;
        }
    }

    #[Groups([
        Utilisateur::GROUP_OUT,
        Demande::GROUP_OUT,
        Utilisateur::AMENAGEMENTS_UTILISATEURS_OUT,
        Amenagement::GROUP_OUT,
    ])]
    public Composante $composante {
        get {
            $prop = new ReflectionProperty(self::class, 'composante');
            if (!$prop->isInitialized($this) && $this->entity !== null && $this->entity->getComposante()) {
                $this->composante = new Composante($this->entity->getComposante());
            }
            return $this->composante;
        }
    }

    #[Groups([
        Utilisateur::GROUP_OUT,
        Demande::GROUP_OUT,
        Utilisateur::AMENAGEMENTS_UTILISATEURS_OUT,
        Amenagement::GROUP_OUT,
    ])]
    public string $libelle {
        get {
            $prop = new ReflectionProperty(self::class, 'libelle');
            if (!$prop->isInitialized($this) && $this->entity !== null) {
                $this->libelle = $this->entity->getLibelle();
            }
            return $this->libelle ?? '';
        }
    }

    #[Groups([Utilisateur::GROUP_OUT])]
    public string $codeExterne {
        get {
            $prop = new ReflectionProperty(self::class, 'codeExterne');
            if (!$prop->isInitialized($this) && $this->entity !== null) {
                $this->codeExterne = $this->entity->getCodeExterne();
            }
            return $this->codeExterne ?? '';
        }
    }

    #[Groups([Utilisateur::GROUP_OUT])]
    public ?string $niveau {
        get {
            $prop = new ReflectionProperty(self::class, 'niveau');
            if (!$prop->isInitialized($this) && $this->entity !== null) {
                $this->niveau = $this->entity->getNiveau();
            }
            return $this->niveau ?? null;
        }
    }

    #[Groups([Utilisateur::GROUP_OUT])]
    public ?string $discipline {
        get {
            $prop = new ReflectionProperty(self::class, 'discipline');
            if (!$prop->isInitialized($this) && $this->entity !== null) {
                $this->discipline = $this->entity->getDiscipline();
            }
            return $this->discipline ?? null;
        }
    }

    #[Groups([Utilisateur::GROUP_OUT])]
    public ?string $diplome {
        get {
            $prop = new ReflectionProperty(self::class, 'diplome');
            if (!$prop->isInitialized($this) && $this->entity !== null) {
                $this->diplome = $this->entity->getDiplome();
            }
            return $this->diplome ?? null;
        }
    }

    public function __construct(
        private readonly ?\App\Entity\Formation $entity = null,
    ) {}
}
