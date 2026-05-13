<?php

namespace App\Security\Voter;

use App\ApiResource\Utilisateur;
use App\Entity\MembreCommission;
use App\Repository\DemandeRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class VoirUtilisateurVoter extends Voter
{
    public function __construct(
        private readonly Security $security,
        private readonly DemandeRepository $demandeRepository,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === Utilisateur::VOIR_UTILISATEUR;
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null,
    ): bool {
        assert($attribute === Utilisateur::VOIR_UTILISATEUR);
        assert($subject instanceof Utilisateur);

        // on se voit soi-même
        if ($subject->uid === $token->getUserIdentifier()) {
            return true;
        }

        // les planificateurs voient tout le monde
        if ($this->security->isGranted(\App\Entity\Utilisateur::ROLE_PLANIFICATEUR)) {
            return true;
        }

        // Tout le monde accédant à la plateforme peut voir les infos des gestionnaires - à affiner ?
        if ($this->security->isGranted(\App\Entity\Utilisateur::ROLE_GESTIONNAIRE, $subject)) {
            return true;
        }

        $utilisateurCourant = $this->security->getUser();
        assert($utilisateurCourant instanceof \App\Entity\Utilisateur);

        // Référents de composantes
        if ($this->security->isGranted(\App\Entity\Utilisateur::ROLE_REFERENT_COMPOSANTE)) {
            //on vérifie si l'utilisateur est inscrit sur la composante gérée
            $composantesUtilisateurCourant = $utilisateurCourant->getComposantes();
            $composantesEtudiant = array_reduce(
                $subject->inscriptions,
                fn($acc, $inscription) => [...$acc, $inscription->formation->composante->id],
                [],
            );
            foreach ($composantesUtilisateurCourant as $composanteUtilisateurCourant) {
                if (in_array($composanteUtilisateurCourant->getId(), $composantesEtudiant)) {
                    return true;
                }
            }
        }

        // Membres de commission - voient les étudiants de leurs commissions
        if ($this->security->isGranted(\App\Entity\Utilisateur::ROLE_MEMBRE_COMMISSION)) {
            $commissionsAutorisees = array_map(fn(MembreCommission $commission) => $commission
                ->getCommission()
                ->getId(), $utilisateurCourant->getMembreCommissions()->toArray());

            $demandesEtudiant = $this->demandeRepository->findByUid($subject->uid);

            if (array_any(array: $demandesEtudiant, callback: fn($demande) => in_array(
                needle: $demande->commission?->getId(),
                haystack: $commissionsAutorisees,
            ))) {
                return true;
            }
        }

        // demandeur : on ne peut voir que les gens liés à ses événements (intervenant, enseignant...)
        $vote?->addReason('Un simple demandeur ne peut pas voir tous les autres utilisateurs.');
        return false;
    }
}
