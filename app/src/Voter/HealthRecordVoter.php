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
            self::EDIT => $this->canEdit($healthRecord, $user),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function canView(HealthRecord $healthRecord, User $user): bool
    {
        if ($this->canEdit($healthRecord, $user)) {
            return true;
        }

        return false;
    }

    private function canEdit(HealthRecord $healthRecord, User $user): bool
    {
        return $user === $healthRecord->getUser();
    }
}
