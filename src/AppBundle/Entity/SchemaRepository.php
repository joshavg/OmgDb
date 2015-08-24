<?php
namespace AppBundle\Entity;

use laniger\Neo4jBundle\Architecture\Neo4jRepository;
use laniger\Neo4jBundle\Architecture\Neo4jClientConsumer;
use laniger\Neo4jBundle\Architecture\Neo4jClientWrapper;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Everyman\Neo4j\Node;

class SchemaRepository
{
    use Neo4jRepository {
        Neo4jRepository::__construct as neo;
    }

    /**
     *
     * @var User
     */
    private $user;

    /**
     *
     * @var AttributeRepository
     */
    private $attrrepo;

    public function __construct(Neo4jClientWrapper $client, TokenStorage $storage, AttributeRepository $attrrepo)
    {
        $this->neo($client);
        $this->user = $storage->getToken()->getUser();
        $this->attrrepo = $attrrepo;
    }

    public function newSchema(Schema $schema)
    {
        $this->client->cypher('
             MATCH (u:user)
             WHERE u.name = {username}
            CREATE (n:schema)-[r:created_by]->(u)
               SET n.name = {name},
                   n.created_at = {date}
        ', [
            'name' => $schema->getName(),
            'username' => $this->user->getUsername(),
            'date' => date(\DateTime::ISO8601)
        ]);
    }

    private function createSchemaFromRow(Node $row)
    {
        $schema = new Schema();
        $schema->setName($row->getProperty('name'));
        $schema->setCreatedAt(\DateTime::createFromFormat(\DateTime::ISO8601, $row->getProperty('created_at')));
        return $schema;
    }

    public function fetchForOverview()
    {
        $dat = $this->client->cypher('
             MATCH (n:schema)-[r:created_by]->(u:user)
             WHERE u.name = {username}
            RETURN n
            ORDER BY n.name
        ', [
            'username' => $this->user->getUsername()
        ]);

        $return = [];
        foreach ($dat as $row) {
            $return[] = $this->createSchemaFromRow($row['n']);
        }

        return $return;
    }

    public function fetch($name)
    {
        $dat = $this->client->cypher('
            MATCH (n:schema)-[r:created_by]->(u:user)
            WHERE u.name = {user}
              AND n.name = {name}
           RETURN n
        ', [
            'user' => $this->user->getUsername(),
            'name' => $name
        ])[0];

        $schema = $this->createSchemaFromRow($dat['n']);
        $schema->setAttributes($this->attrrepo->getForSchema($schema));
        return $schema;
    }
}