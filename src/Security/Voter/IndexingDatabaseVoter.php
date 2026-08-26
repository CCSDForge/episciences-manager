<?php

namespace App\Security\Voter;

use App\Entity\IndexingDatabase;
use App\Entity\Review;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for IndexingDatabase permissions.
 *
 * Permission matrix:
 * - epiadmin: Full access (create validated, validate/reject, edit, delete)
 * - administrator, chief_editor: Can propose new databases (created as Pending)
 * - REVIEW_EDIT roles: Can associate/dissociate validated databases with their review
 *
 * @extends Voter<string, IndexingDatabase|Review|array<string, mixed>|null>
 */
final class IndexingDatabaseVoter extends Voter
{
    // Admin-only actions (epiadmin role required, no subject needed)
    public const ADMIN_LIST = 'INDEXING_DATABASE_ADMIN_LIST';
    public const ADMIN_CREATE = 'INDEXING_DATABASE_ADMIN_CREATE';
    public const ADMIN_EDIT = 'INDEXING_DATABASE_ADMIN_EDIT';
    public const ADMIN_DELETE = 'INDEXING_DATABASE_ADMIN_DELETE';
    public const ADMIN_VALIDATE = 'INDEXING_DATABASE_ADMIN_VALIDATE';

    // Review-scoped actions (requires review context)
    public const PROPOSE = 'INDEXING_DATABASE_PROPOSE';
    public const ASSOCIATE = 'INDEXING_DATABASE_ASSOCIATE';

    private const ADMIN_ATTRIBUTES = [
        self::ADMIN_LIST,
        self::ADMIN_CREATE,
        self::ADMIN_EDIT,
        self::ADMIN_DELETE,
        self::ADMIN_VALIDATE,
    ];

    private const REVIEW_SCOPED_ATTRIBUTES = [
        self::PROPOSE,
        self::ASSOCIATE,
    ];

    // Roles that can propose a new indexing database (created as PENDING)
    private const PROPOSE_ROLES = ['epiadmin', 'administrator', 'chief_editor'];

    // Roles that can associate/dissociate validated databases with a review
    private const ASSOCIATE_ROLES = ['epiadmin', 'administrator', 'chief_editor', 'secretary'];

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Admin actions don't require a subject (or accept IndexingDatabase)
        if (in_array($attribute, self::ADMIN_ATTRIBUTES, true)) {
            return $subject === null || $subject instanceof IndexingDatabase;
        }

        // Review-scoped actions require a Review or array with rvid
        if (in_array($attribute, self::REVIEW_SCOPED_ATTRIBUTES, true)) {
            return is_array($subject) || $subject instanceof Review;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // Admin-only actions: check for epiadmin role
        if (in_array($attribute, self::ADMIN_ATTRIBUTES, true)) {
            return $this->isEpiadmin($user);
        }

        // Review-scoped actions: extract rvid and check role
        $rvid = $this->extractRvid($subject);
        if ($rvid === null) {
            return false;
        }

        return match ($attribute) {
            self::PROPOSE => $this->canPropose($user, $rvid),
            self::ASSOCIATE => $this->canAssociate($user, $rvid),
            default => false,
        };
    }

    /**
     * Check if the user has the epiadmin role (global admin).
     */
    private function isEpiadmin(User $user): bool
    {
        foreach ($user->getRolesDetails() as $role) {
            if ($role['ROLEID'] === 'epiadmin') {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the user can propose a new indexing database for a review.
     * Requires administrator role on the specific review, or epiadmin.
     */
    private function canPropose(User $user, int $rvid): bool
    {
        foreach ($user->getRolesDetails() as $role) {
            // epiadmin can propose for any review
            if ($role['ROLEID'] === 'epiadmin') {
                return true;
            }
            // administrator can propose for their own review
            if (in_array($role['ROLEID'], self::PROPOSE_ROLES, true) && (int)$role['RVID'] === $rvid) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the user can associate/dissociate validated databases with a review.
     * Requires REVIEW_EDIT-level roles on the specific review.
     */
    private function canAssociate(User $user, int $rvid): bool
    {
        foreach ($user->getRolesDetails() as $role) {
            // epiadmin can associate for any review
            if ($role['ROLEID'] === 'epiadmin') {
                return true;
            }
            // Other roles need to match the specific review
            if (in_array($role['ROLEID'], self::ASSOCIATE_ROLES, true) && (int)$role['RVID'] === $rvid) {
                return true;
            }
        }
        return false;
    }

    /**
     * Extract rvid from subject (Review entity or array).
     */
    private function extractRvid(mixed $subject): ?int
    {
        if ($subject instanceof Review) {
            return $subject->getRvid();
        }

        if (is_array($subject) && isset($subject['rvid'])) {
            return (int)$subject['rvid'];
        }

        return null;
    }
}
