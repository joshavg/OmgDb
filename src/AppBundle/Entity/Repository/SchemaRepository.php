<?php
namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Schema;
use AppBundle\Entity\User;
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

    /**
     * @var DateFactory
     */
    private $dateFactory;

    public function __construct(Neo4jClientWrapper $client, TokenStorage $storage,
                                DateFactory $dateFactory)
    {
        parent::__construct($client);
        $this->tokenStorage = $storage;
        $this->dateFactory = $dateFactory;
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
            'date' => $this->dateFactory->nowString(),
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
        $schema->setCreatedAt($this->dateFactory->fromString($row->get('created_at')));
        $schema->setUid($row->get('uid'));
        return $schema;
    }

    public function fetchByUid($uid)
    {
        $dat = $this->getClient()->cypher('
            MATCH (n:schema)
            WHERE n.uid = {uid}
           RETURN n
        ', [
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

    public function deleteByUid($uid)
    {
        $this->getClient()->cypher('
            MATCH (s:schema)-[r:created_by]->(:user)
            WHERE s.uid = {uid}
         OPTIONAL MATCH
                  (a:attribute)-[ar:attribute_of]->(s:schema),
                  (a)-[cr:created_by]->(:user)
            DELETE r, ar, cr, a, s
        ', [
            'uid' => $uid
        ]);
    }

    public function update(Schema $schema)
    {
        $this->getClient()->cypher('
            MATCH (s:schema)
            WHERE s.uid = {uid}
              SET s.name = {name}
        ', [
            'uid' => $schema->getUid(),
            'name' => $schema->getName()
        ]);
    }
}
