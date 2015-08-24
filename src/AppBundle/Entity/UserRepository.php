<?php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use laniger\Neo4jBundle\Architecture\Neo4jClientConsumer;

/**
 * UserRepository
 */
class UserRepository
{
    use Neo4jClientConsumer;

    public function persistUser(User $user)
    {
        $this->client->cypher('
            MATCH (n:user)
            WHERE n.name = {name}
              SET n.password = {pw},
                  n.email = {email}
        ', [
            'name' => $user->getName(),
            'pw' => $user->getPassword(),
            'email' => $user->getEmail()
        ]);
    }
}
