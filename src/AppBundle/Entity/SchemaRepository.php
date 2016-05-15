<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\Client\Formatter\Type\Node;
use laniger\Neo4jBundle\Architecture\Neo4jClientWrapper;
use laniger\Neo4jBundle\Architecture\Neo4jRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class SchemaRepository extends Neo4jRepository
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(Neo4jClientWrapper $client, TokenStorage $storage)
    {
        parent::__construct($client);
        $this->tokenStorage = $storage;
    }

    /**
     * @return User
     */
    private function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
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
            'username' => $this->getUser()->getUsername(),
            'date' => date(\DateTime::ISO8601)
        ]);
    }

    public function fetchAllForCurrentUser()
    {
        $dat = $this->getClient()->cypher('
             MATCH (n:schema)-[r:created_by]->(u:user)
             WHERE u.name = {username}
            RETURN n
            ORDER BY LOWER(n.name)
        ', [
            'username' => $this->getUser()->getUsername()
        ])->records();

        $return = [];
        foreach ($dat as $row) {
            $return[] = $this->createSchemaFromRow($row->get('n'));
        }

        return $return;
    }

    private function createSchemaFromRow(Node $row)
    {
        $schema = new Schema();
        $schema->setName($row->get('name'));
        $schema->setCreatedAt(\DateTime::createFromFormat(\DateTime::ISO8601,
            $row->get('created_at')));
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
            'user' => $this->getUser()->getUsername(),
            'name' => $name
        ])->records()[0];

        $schema = $this->createSchemaFromRow($dat->get('n'));
        return $schema;
    }

    public function isNameUniqueForCurrentUser($name)
    {
        $dat = $this->getClient()->cypher('
            MATCH (s:schema)-[r:created_by]->(u:user)
            WHERE s.name = {name}
              AND u.name = {user}
            RETURN COUNT(s) AS cnt
        ', [
            'user' => $this->getUser()->getUsername(),
            'name' => $name
        ])->firstRecord()->get('cnt');

        return $dat < 1;
    }
}
