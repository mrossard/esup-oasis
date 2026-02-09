<?php

namespace App\State;

use App\ApiResource\Campus;
use App\ApiResource\CategorieAmenagement;
use App\ApiResource\Commission;
use App\ApiResource\ProfilBeneficiaire;
use App\ApiResource\Tag;
use App\ApiResource\TypeAmenagement;
use Override;

class ReferentielTransformer extends AbstractTransformer
{
    #[Override] 
    protected function registerTransformations(): void
    {
        $this->transformerService->addTransformation(\App\Entity\Campus::class, Campus::class, $this->transformCampus(...));
        $this->transformerService->addTransformation(\App\Entity\ProfilBeneficiaire::class, ProfilBeneficiaire::class, $this->transformProfilBeneficiaire(...));
        $this->transformerService->addTransformation(\App\Entity\TypeAmenagement::class, TypeAmenagement::class, $this->transformTypeAmenagement(...));
        $this->transformerService->addTransformation(\App\Entity\CategorieAmenagement::class, CategorieAmenagement::class, $this->transformCategorieAmenagement(...));
        $this->transformerService->addTransformation(\App\Entity\Tag::class, Tag::class, $this->transformTag(...));
        $this->transformerService->addTransformation(\App\Entity\Commission::class, Commission::class, $this->transformCommission(...));
    }

    public function transformCampus(\App\Entity\Campus $entity): Campus
    {
        return new Campus($entity);
    }

    public function transformCommission(\App\Entity\Commission $entity): Commission
    {
        return new Commission($entity);
    }

    public function transformProfilBeneficiaire(\App\Entity\ProfilBeneficiaire $entity): ProfilBeneficiaire
    {
        return new ProfilBeneficiaire($entity);
    }

    public function transformTypeAmenagement(\App\Entity\TypeAmenagement $entity): TypeAmenagement
    {
        return new TypeAmenagement($entity);
    }

    public function transformCategorieAmenagement(\App\Entity\CategorieAmenagement $entity): CategorieAmenagement
    {
        return new CategorieAmenagement($entity);
    }

    public function transformTag(\App\Entity\Tag $entity): Tag
    {
        return new Tag($entity);
    }

    #[Override] 
    public function transform($entity): mixed
    {
        return null;
    }
}
