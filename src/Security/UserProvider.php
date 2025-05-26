<?php
namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use \Symfony\Component\Security\Core\User\UserProviderInterface;



class UserProvider implements UserProviderInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function refreshUser(UserInterface $user): UserInterface
    {
        // Implement refreshUser() method.
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        // Implement supportsClass() method.
        return $class === User::class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        if ($identifier === '__NO_USER__') {
            $user = new User();
            $user->setUsername('__NO_USER__');
            $user->setRoles(['ROLE_ANO']);
            return $user;
        }
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $identifier]);
        //dump($user);

        if (!$user) {
            throw new \Exception("Utilisateur avec username=$identifier introuvable");
        }

        // Charger les rôles depuis USER_ROLES en utilisant DBAL
        $conn = $this->em->getConnection();
        //$roles = $conn->fetchFirstColumn('SELECT ROLEID, RVID FROM USER_ROLES WHERE UID = ?', [$user->getUid()]);
        $rows = $conn->fetchAllAssociative('SELECT ROLEID, RVID FROM USER_ROLES WHERE UID = ?', [$user->getUid()]);
        //dump($rows);
        $roles = array_column($rows, 'ROLEID');
        if (empty($roles)) {
            $roles = ['ROLE_USER'];
        }
        //dump($roles);
        $user->setRoles($roles ?? []);
        $user->setRolesDetails($rows ?? []);

        return $user;
    }
}