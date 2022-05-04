<?php

declare(strict_types=1);

namespace App\Voter;

use App\Entity\HealthRecord;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class HealthRecordVoter extends Voter
{
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute !== self::EDIT) {
            return false;
        }

        if (!$subject instanceof HealthRecord) {
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

        /** @var HealthRecord $healthRecord */
        $healthRecord = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($healthRecord, $user),
            self::EDIT => $this->canEdit($user),
            default => throw new \LogicException('Access denied'),
        };
    }

    private function canView(HealthRecord $healthRecord, User $user): bool
    {
        return $user === $healthRecord->getUser() && in_array(User::ROLE_PATIENT, $user->getRoles(), true);
    }

    private function canEdit(User $user): bool
    {
        if (in_array(User::ROLE_DOCTOR, $user->getRoles(), true)) {
            return true;
        }

        if (in_array(User::ROLE_ADMIN, $user->getRoles(), true)) {
            return true;
        }

        return false;
    }
}
