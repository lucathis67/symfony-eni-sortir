<?php

namespace App\Security;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SortieVoter extends Voter
{
    const EDIT = 'edit';

    /**
     * @var EtatRepository
     */
    private $etatRepository;

    /**
     * @param EtatRepository $etatRepository
     */
    public function __construct(EtatRepository $etatRepository)
    {
        $this->etatRepository = $etatRepository;
    }

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::EDIT])) {
            return false;
        }

        // only vote on `Sortie` objects
        if (!$subject instanceof Sortie) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var Participant $participant */
        $participant = $token->getUser();

        if (!$participant instanceof Participant) {
            // the participant must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Sortie object, thanks to `supports()`
        /** @var Sortie $sortie */
        $sortie = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($sortie, $participant);
        }

        throw new \LogicException('Ce code ne doit pas être atteint!');
    }

    private function canEdit(Sortie $sortie, Participant $participant): bool
    {
        return $participant === $sortie->getOrganisateur()
            && ($sortie->getEtat() === $this->etatRepository->findOneBy(['libelle' => 'Ouverte'])
                || $sortie->getEtat() === $this->etatRepository->findOneBy(['libelle' => 'Créée']));
    }
}