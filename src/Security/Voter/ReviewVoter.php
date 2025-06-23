<?php

namespace App\Security\Voter;

use App\Entity\Review;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

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

    private function canViewDetail(User $user, int $rvid): bool
    {
        //only epiadmin can see the details
        foreach ($user->getRolesDetails() as $role) {
            if ($role['ROLEID'] === 'epiadmin' && (int)$role['RVID'] === $rvid) {
                return true;
            }
        }
        return false;
    }

    private function canEdit(User $user, int $rvid): bool
    {
        //Only epiadmin can edit
        foreach ($user->getRolesDetails() as $role) {
            if ($role['ROLEID'] === 'epiadmin' && (int)$role['RVID'] === $rvid) {
                return true;
            }
        }
        return false;
    }
}
