<?php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use \Symfony\Component\Security\Core\User\UserProviderInterface;



class UserProvider implements UserProviderInterface {

    public function refreshUser(UserInterface $user): User
    {
        // Implement refreshUser() method.
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }
        return $this->loadUserByUid($user->getUid());
    }

    public function supportsClass(string $class): bool
    {
        // Implement supportsClass() method.
        return $class === User::class;
    }

    public function loadUserByUid(int $uid): User
    {
        // Implement loadUserByUid() method.
        $user = new User();
        $user->setUid($uid);
        return $user;
    }
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $uid = (int)$identifier;
        return $this->loadUserByUid($uid);
    }
}