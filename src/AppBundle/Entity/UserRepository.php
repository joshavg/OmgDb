<?php
namespace AppBundle\Entity;

use HireVoice\Neo4j\Repository;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * UserRepository
 */
class UserRepository extends Repository implements UserProviderInterface
{

    public function loadUserByUsername($username)
    {
        $user = $this->findOneBy([
            'name' => $username
        ]);

        if (null === $user) {
            $message = sprintf('Unable to find an active admin AppBundle:User object identified by "%s".', $username);
            throw new UsernameNotFoundException($message);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (! $this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->findOneBy([
            'name' => $user->getUsername()
        ]);
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}
