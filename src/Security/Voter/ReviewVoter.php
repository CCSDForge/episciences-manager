<?php

namespace App\Security\Voter;

use App\Entity\Review;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Review|array<string, mixed>>
 */
final class ReviewVoter extends Voter
{
    public const VIEW = 'REVIEW_VIEW';
    public const EDIT = 'REVIEW_EDIT';
    public const EDIT_FRONTEND_SETTINGS = 'REVIEW_EDIT_FRONTEND_SETTINGS';

    public const EDIT_BACKOFFICE_SETTINGS = 'REVIEW_EDIT_BACKOFFICE_SETTINGS';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::EDIT_FRONTEND_SETTINGS, self::EDIT_BACKOFFICE_SETTINGS])
            && (is_array($subject) || $subject instanceof Review);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        $rvid = is_array($subject) ? (int)$subject['rvid'] : $subject->getRvid();

        switch ($attribute) {
            case self::VIEW:
                return $this->canViewDetail($user, $rvid);
            case self::EDIT:
                return $this->canEdit($user, $rvid);
            case self::EDIT_FRONTEND_SETTINGS:
                return $this->canEditFrontendSettings($user, $rvid);
            case self::EDIT_BACKOFFICE_SETTINGS:
                return $this->canEditBackofficeSettings($user, $rvid);
        }
        return false;
    }

    private const VIEW_ROLES = ['epiadmin', 'administrator', 'chief_editor', 'secretary'];
    private const EDIT_ROLES = ['epiadmin', 'administrator', 'chief_editor', 'secretary'];
    private const EDIT_FRONTEND_SETTINGS_ROLES = ['epiadmin'];
    private const EDIT_BACKOFFICE_SETTINGS_ROLES = ['epiadmin','administrator'];

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

    private function canEditFrontendSettings(User $user, int $rvid): bool
    {
        foreach ($user->getRolesDetails() as $role) {
            if (in_array($role['ROLEID'], self::EDIT_FRONTEND_SETTINGS_ROLES, true) && (int)$role['RVID'] === $rvid) {
                return true;
            }
        }
        return false;
    }

    private function canEditBackofficeSettings(User $user, int $rvid): bool
    {
        foreach ($user->getRolesDetails() as $role) {
            if (in_array($role['ROLEID'], self::EDIT_BACKOFFICE_SETTINGS_ROLES, true) && (int)$role['RVID'] === $rvid) {
                return true;
            }
        }
        return false;
    }
}
