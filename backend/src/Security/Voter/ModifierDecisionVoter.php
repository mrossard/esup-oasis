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

use App\ApiResource\DecisionAmenagementExamens;
use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ModifierDecisionVoter extends Voter
{

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === DecisionAmenagementExamens::MODIFIER_DECISION;
    }

    /**
     * @param string                     $attribute
     * @param DecisionAmenagementExamens $subject
     * @param TokenInterface             $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (in_array(Utilisateur::ROLE_ADMIN, $token->getRoleNames()) &&
            in_array($subject->etat, [\App\Entity\DecisionAmenagementExamens::ETAT_VALIDE, \App\Entity\DecisionAmenagementExamens::ETAT_EDITION_DEMANDEE])) {
            return true;
        }

        if (in_array(Utilisateur::ROLE_GESTIONNAIRE, $token->getRoleNames()) && $subject->etat === \App\Entity\DecisionAmenagementExamens::ETAT_VALIDE) {
            return true;
        }

        return false;
    }
}