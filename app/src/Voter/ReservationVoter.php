<?php

declare(strict_types=1);

namespace App\Voter;

use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ReservationVoter extends Voter
{
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute !== self::EDIT) {
            return false;
        }

        if (!$subject instanceof Reservation) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Reservation $reservation */
        $reservation = $subject;

       return match ($attribute) {
            self::VIEW => $this->canView($reservation, $user),
            self::EDIT => $this->canEdit($reservation, $user),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function canView(Reservation $reservation, User $user): bool
    {
        if ($this->canEdit($reservation, $user)) {
            return true;
        }

        return false;
    }

    private function canEdit(Reservation $reservation, User $user): bool
    {
        return $user === $reservation->getUser();
    }
}
