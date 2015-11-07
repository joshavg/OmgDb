<?php
namespace AppBundle\Entity;

use laniger\Neo4jBundle\Architecture\Neo4jRepository;
use laniger\Neo4jBundle\Architecture\Neo4jClientWrapper;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class AttributeRepository
{
    use Neo4jRepository {
        Neo4jRepository::__construct as neo;
    }

    /**
     * @var User
     */
    private $user;

    public function __construct(Neo4jClientWrapper $client, TokenStorage $storage)
    {
        $this->neo($client);
        $this->user = $storage->getToken()->getUser();
    }

    public function getForSchema(Schema $schema)
    {
        $attr = $this->client->cypher('
            MATCH (s:schema)<-[:attribute_of]->(a:attribute)
            WHERE s.name = {name}
           RETURN a
            ORDER BY s.name
        ', [
            'name' => $schema->getName()
        ]);

        $attributes = [];
        foreach ($attr->getRows()['a'] as $row) {
            $a = $this->createFromRow($row);
            $a->setSchema($schema);
            $attributes[] = $a;
        }
        return $attributes;
    }

    private function createFromRow($row)
    {
        $a = new Attribute();
        $a->setName($row['name']);
        $a->setCreatedAt(\DateTime::createFromFormat(\DateTime::ISO8601, $row['created_at']));
        $a->setDataType(AttributeDataType::getByName($row['type']));
        return $a;
    }
}
