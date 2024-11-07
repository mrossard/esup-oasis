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

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class BeneficiaireNonNullConstraint extends Constraint
{
    public string $message = "Ce type d'événement nécessite un bénéficiaire";

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}