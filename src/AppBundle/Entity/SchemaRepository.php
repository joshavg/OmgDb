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
                   s.created_at = {date},
                   s.uid = {uid}
        ', [
            'name' => $schema->getName(),
            'username' => $this->getUser()->getUsername(),
            'date' => date(\DateTime::ISO8601),
            'uid' => $schema->getUid()
        ]);
    }

    public function fetchAllForCurrentUser()
    {
        $dat = $this->getClient()->cypher('
             MATCH (n:schema)-[:created_by]->(u:user)
             WHERE u.name = {username}
            RETURN n
            ORDER BY LOWER(n.name)
        ', [
            'username' => $this->getUser()->getUsername()
        ])->records();

        $return = [];
        foreach ($dat as $row) {
            $schema = $this->createSchemaFromRow($row->get('n'));
            $return[] = $schema;
        }

        return $return;
    }

    /**
     * @param Node $row
     * @return Schema
     */
    private function createSchemaFromRow(Node $row)
    {
        $schema = new Schema();
        $schema->setName($row->get('name'));
        $schema->setCreatedAt(\DateTime::createFromFormat(\DateTime::ISO8601,
            $row->get('created_at')));
        $schema->setUid($row->get('uid'));
        return $schema;
    }

    public function fetchByUid($uid)
    {
        $dat = $this->getClient()->cypher('
            MATCH (n:schema)-[r:created_by]->(u:user)
            WHERE u.name = {user}
              AND n.uid = {uid}
           RETURN n
        ', [
            'user' => $this->getUser()->getUsername(),
            'uid' => $uid
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
