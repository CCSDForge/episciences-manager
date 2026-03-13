<?php

namespace App\Security\Voter;

use App\Entity\Review;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ReviewVoter extends Voter
{
    public const VIEW = 'REVIEW_VIEW';
    public const EDIT = 'REVIEW_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && (is_array($subject) || $subject instanceof Review);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        if (is_array($subject)) {
            $rvid = (int)$subject['rvid'];
        } elseif ($subject instanceof Review) {
            $rvid = $subject->getRvid();
        } else {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::VIEW:
                return $this->canViewDetail($user, $rvid);
            case self::EDIT:
                return $this->canEdit($user, $rvid);
        }
        return false;
    }

    private const VIEW_ROLES = ['epiadmin', 'administrator', 'chief_editor', 'secretary'];
    private const EDIT_ROLES = ['epiadmin'];

    private function canViewDetail(User $user, int $rvid): bool
    {
        foreach ($user->getRolesDetails() as $role) {
            if (in_array($role['ROLEID'], self::VIEW_ROLES, true) && (int)$role['RVID'] === $rvid) {
                return true;
            }
        }
        return false;
    }

    private function canEdit(User $user, int $rvid): bool
    {
        foreach ($user->getRolesDetails() as $role) {
            if (in_array($role['ROLEID'], self::EDIT_ROLES, true) && (int)$role['RVID'] === $rvid) {
                return true;
            }
        }
        return false;
    }
}
