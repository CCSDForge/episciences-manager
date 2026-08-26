<?php

use App\Entity\Review;
use App\Entity\User;
use App\Security\Voter\IndexingDatabaseVoter;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

function createUserWithRoles(array $rolesDetails): User
{
    $user = new User();
    $user->setUid(1);
    $user->setUsername('testuser');
    $user->setEmail('test@example.com');
    $user->setRolesDetails($rolesDetails);
    return $user;
}

function createToken(User $user): UsernamePasswordToken
{
    return new UsernamePasswordToken($user, 'main', $user->getRoles());
}

// ============================================
// Admin-only actions (epiadmin required)
// ============================================

it('grants ADMIN_LIST to epiadmin user', function () {
    $user = createUserWithRoles([['ROLEID' => 'epiadmin', 'RVID' => 0]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, null, [IndexingDatabaseVoter::ADMIN_LIST]);
    expect($result)->toBe(VoterInterface::ACCESS_GRANTED);
});

it('denies ADMIN_LIST to administrator user', function () {
    $user = createUserWithRoles([['ROLEID' => 'administrator', 'RVID' => 1]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, null, [IndexingDatabaseVoter::ADMIN_LIST]);
    expect($result)->toBe(VoterInterface::ACCESS_DENIED);
});

it('grants ADMIN_CREATE to epiadmin user', function () {
    $user = createUserWithRoles([['ROLEID' => 'epiadmin', 'RVID' => 0]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, null, [IndexingDatabaseVoter::ADMIN_CREATE]);
    expect($result)->toBe(VoterInterface::ACCESS_GRANTED);
});

it('denies ADMIN_CREATE to administrator user', function () {
    $user = createUserWithRoles([['ROLEID' => 'administrator', 'RVID' => 1]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, null, [IndexingDatabaseVoter::ADMIN_CREATE]);
    expect($result)->toBe(VoterInterface::ACCESS_DENIED);
});

it('grants ADMIN_VALIDATE to epiadmin user', function () {
    $user = createUserWithRoles([['ROLEID' => 'epiadmin', 'RVID' => 0]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, null, [IndexingDatabaseVoter::ADMIN_VALIDATE]);
    expect($result)->toBe(VoterInterface::ACCESS_GRANTED);
});

it('denies ADMIN_VALIDATE to non-epiadmin users', function () {
    $user = createUserWithRoles([
        ['ROLEID' => 'administrator', 'RVID' => 1],
        ['ROLEID' => 'chief_editor', 'RVID' => 2],
    ]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, null, [IndexingDatabaseVoter::ADMIN_VALIDATE]);
    expect($result)->toBe(VoterInterface::ACCESS_DENIED);
});

it('grants ADMIN_EDIT to epiadmin user', function () {
    $user = createUserWithRoles([['ROLEID' => 'epiadmin', 'RVID' => 0]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, null, [IndexingDatabaseVoter::ADMIN_EDIT]);
    expect($result)->toBe(VoterInterface::ACCESS_GRANTED);
});

it('grants ADMIN_DELETE to epiadmin user', function () {
    $user = createUserWithRoles([['ROLEID' => 'epiadmin', 'RVID' => 0]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, null, [IndexingDatabaseVoter::ADMIN_DELETE]);
    expect($result)->toBe(VoterInterface::ACCESS_GRANTED);
});

// ============================================
// PROPOSE action (administrator+ on review)
// ============================================

it('grants PROPOSE to administrator for their review', function () {
    $user = createUserWithRoles([['ROLEID' => 'administrator', 'RVID' => 1]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, ['rvid' => 1], [IndexingDatabaseVoter::PROPOSE]);
    expect($result)->toBe(VoterInterface::ACCESS_GRANTED);
});

it('denies PROPOSE to administrator for other review', function () {
    $user = createUserWithRoles([['ROLEID' => 'administrator', 'RVID' => 1]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, ['rvid' => 2], [IndexingDatabaseVoter::PROPOSE]);
    expect($result)->toBe(VoterInterface::ACCESS_DENIED);
});

it('grants PROPOSE to epiadmin for any review', function () {
    $user = createUserWithRoles([['ROLEID' => 'epiadmin', 'RVID' => 0]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, ['rvid' => 99], [IndexingDatabaseVoter::PROPOSE]);
    expect($result)->toBe(VoterInterface::ACCESS_GRANTED);
});

it('grants PROPOSE to chief_editor for their review', function () {
    $user = createUserWithRoles([['ROLEID' => 'chief_editor', 'RVID' => 1]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, ['rvid' => 1], [IndexingDatabaseVoter::PROPOSE]);
    expect($result)->toBe(VoterInterface::ACCESS_GRANTED);
});

it('denies PROPOSE to secretary', function () {
    $user = createUserWithRoles([['ROLEID' => 'secretary', 'RVID' => 1]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, ['rvid' => 1], [IndexingDatabaseVoter::PROPOSE]);
    expect($result)->toBe(VoterInterface::ACCESS_DENIED);
});

// ============================================
// ASSOCIATE action (REVIEW_EDIT roles)
// ============================================

it('grants ASSOCIATE to administrator for their review', function () {
    $user = createUserWithRoles([['ROLEID' => 'administrator', 'RVID' => 1]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, ['rvid' => 1], [IndexingDatabaseVoter::ASSOCIATE]);
    expect($result)->toBe(VoterInterface::ACCESS_GRANTED);
});

it('grants ASSOCIATE to chief_editor for their review', function () {
    $user = createUserWithRoles([['ROLEID' => 'chief_editor', 'RVID' => 1]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, ['rvid' => 1], [IndexingDatabaseVoter::ASSOCIATE]);
    expect($result)->toBe(VoterInterface::ACCESS_GRANTED);
});

it('grants ASSOCIATE to secretary for their review', function () {
    $user = createUserWithRoles([['ROLEID' => 'secretary', 'RVID' => 1]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, ['rvid' => 1], [IndexingDatabaseVoter::ASSOCIATE]);
    expect($result)->toBe(VoterInterface::ACCESS_GRANTED);
});

it('grants ASSOCIATE to epiadmin for any review', function () {
    $user = createUserWithRoles([['ROLEID' => 'epiadmin', 'RVID' => 0]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, ['rvid' => 99], [IndexingDatabaseVoter::ASSOCIATE]);
    expect($result)->toBe(VoterInterface::ACCESS_GRANTED);
});

it('denies ASSOCIATE to user for other review', function () {
    $user = createUserWithRoles([['ROLEID' => 'administrator', 'RVID' => 1]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, ['rvid' => 2], [IndexingDatabaseVoter::ASSOCIATE]);
    expect($result)->toBe(VoterInterface::ACCESS_DENIED);
});

// ============================================
// Subject types: Review entity vs array
// ============================================

it('accepts Review entity as subject for PROPOSE', function () {
    $user = createUserWithRoles([['ROLEID' => 'administrator', 'RVID' => 1]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $review = new Review();
    // Use reflection to set rvid since it's usually set by Doctrine
    $reflection = new ReflectionClass($review);
    $property = $reflection->getProperty('rvid');
    $property->setAccessible(true);
    $property->setValue($review, 1);

    $result = $voter->vote($token, $review, [IndexingDatabaseVoter::PROPOSE]);
    expect($result)->toBe(VoterInterface::ACCESS_GRANTED);
});

it('accepts Review entity as subject for ASSOCIATE', function () {
    $user = createUserWithRoles([['ROLEID' => 'chief_editor', 'RVID' => 1]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $review = new Review();
    $reflection = new ReflectionClass($review);
    $property = $reflection->getProperty('rvid');
    $property->setAccessible(true);
    $property->setValue($review, 1);

    $result = $voter->vote($token, $review, [IndexingDatabaseVoter::ASSOCIATE]);
    expect($result)->toBe(VoterInterface::ACCESS_GRANTED);
});

// ============================================
// Anonymous users
// ============================================

it('denies all actions to anonymous users', function () {
    $voter = new IndexingDatabaseVoter();
    // Create a token with null user (anonymous)
    $token = new class implements \Symfony\Component\Security\Core\Authentication\Token\TokenInterface {
        public function __serialize(): array { return []; }
        public function __unserialize(array $data): void {}
        public function __toString(): string { return ''; }
        public function getUser(): ?\Symfony\Component\Security\Core\User\UserInterface { return null; }
        public function setUser(\Symfony\Component\Security\Core\User\UserInterface $user): void {}
        public function getUserIdentifier(): string { return ''; }
        public function getRoleNames(): array { return []; }
        public function eraseCredentials(): void {}
        public function getAttributes(): array { return []; }
        public function setAttributes(array $attributes): void {}
        public function hasAttribute(string $name): bool { return false; }
        public function getAttribute(string $name): mixed { return null; }
        public function setAttribute(string $name, mixed $value): void {}
    };

    $result = $voter->vote($token, null, [IndexingDatabaseVoter::ADMIN_LIST]);
    expect($result)->toBe(VoterInterface::ACCESS_DENIED);
});

// ============================================
// Unsupported attributes
// ============================================

it('abstains for unsupported attributes', function () {
    $user = createUserWithRoles([['ROLEID' => 'epiadmin', 'RVID' => 0]]);
    $voter = new IndexingDatabaseVoter();
    $token = createToken($user);

    $result = $voter->vote($token, null, ['UNKNOWN_ATTRIBUTE']);
    expect($result)->toBe(VoterInterface::ACCESS_ABSTAIN);
});