<?php
namespace AppBundle\Entity;

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
}
