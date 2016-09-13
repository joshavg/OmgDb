<?php
namespace AppBundle\Entity\Repository;

use AppBundle\Entity\User;
use laniger\Neo4jBundle\Architecture\Neo4jRepository;

/**
 * UserRepository
 */
class UserRepository extends Neo4jRepository
{
    public function persistUser(User $user)
    {
        $this->getClient()->cypher('
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

    public function createUser(User $user)
    {
        $this->getClient()->cypher('
            CREATE (u:user)
               SET u.name = {name},
                   u.password = {password},
                   u.email = {email}
        ', [
            'name' => $user->getUsername(),
            'password' => $user->getPassword(),
            'email' => $user->getEmail()
        ]);
    }
}
