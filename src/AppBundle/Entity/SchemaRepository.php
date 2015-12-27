<?php
namespace AppBundle\Entity;

use laniger\Neo4jBundle\Architecture\Neo4jClientWrapper;
use laniger\Neo4jBundle\Architecture\Neo4jRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class SchemaRepository extends Neo4jRepository
{
    /**
     * @var User
     */
    private $user;

    public function __construct(Neo4jClientWrapper $client, TokenStorage $storage)
    {
        parent::__construct($client);
        $this->user = $storage->getToken()->getUser();
    }

    public function newSchema(Schema $schema)
    {
        $this->getClient()->cypher('
             MATCH (u:user)
             WHERE u.name = {username}
            CREATE (s:schema)-[r:created_by]->(u)
               SET s.name = {name},
                   s.created_at = {date}
        ', [
            'name' => $schema->getName(),
            'username' => $this->user->getUsername(),
            'date' => date(\DateTime::ISO8601)
        ]);
    }

    public function fetchAllForCurrentUser()
    {
        $dat = $this->getClient()->cypher('
             MATCH (n:schema)-[r:created_by]->(u:user)
             WHERE u.name = {username}
            RETURN n
            ORDER BY n.name
        ', [
            'username' => $this->user->getUsername()
        ]);

        $return = [];
        foreach ($dat->getRows()['n'] as $row) {
            $return[] = $this->createSchemaFromRow($row);
        }

        return $return;
    }

    private function createSchemaFromRow($row)
    {
        $schema = new Schema();
        $schema->setName($row['name']);
        $schema->setCreatedAt(\DateTime::createFromFormat(\DateTime::ISO8601, $row['created_at']));
        return $schema;
    }

    public function fetch($name)
    {
        $dat = $this->getClient()->cypher('
            MATCH (n:schema)-[r:created_by]->(u:user)
            WHERE u.name = {user}
              AND n.name = {name}
           RETURN n
        ', [
            'user' => $this->user->getUsername(),
            'name' => $name
        ])->getRows()['n'][0];

        $schema = $this->createSchemaFromRow($dat);
        return $schema;
    }
}
