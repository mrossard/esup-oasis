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

namespace App\Security\Voter;

use App\Entity\Utilisateur;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SwitchUserVoter extends Voter
{
    public function __construct(private readonly Security $security)
    {

    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === 'CAN_SWITCH_USER';
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->security->isGranted(Utilisateur::ROLE_ADMIN);
    }
}