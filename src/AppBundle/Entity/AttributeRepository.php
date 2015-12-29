<?php
namespace AppBundle\Entity;

use laniger\Neo4jBundle\Architecture\Neo4jClientWrapper;
use laniger\Neo4jBundle\Architecture\Neo4jRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class AttributeRepository extends Neo4jRepository
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

    public function getForSchema(Schema $schema)
    {
        $attr = $this->getClient()->cypher('
            MATCH (s:schema)<-[:attribute_of]-(a:attribute)
            WHERE s.name = {name}
           RETURN a
            ORDER BY s.name
        ', [
            'name' => $schema->getName()
        ]);

        $attributes = [];

        if (isset($attr->getRows()['a'])) {
            foreach ($attr->getRows()['a'] as $row) {
                $a = $this->createFromRow($row);
                $a->setSchema($schema);
                $attributes[] = $a;
            }
        }
        return $attributes;
    }

    public function newAttribute(Schema $schema, Attribute $attr)
    {
        $this->getClient()->cypher('
            MATCH (u:user)<-[:created_by]-(s:schema)
            WHERE u.name = {username}
              AND s.name = {schemaname}
           CREATE (a:attribute)-[:attribute_of]->(s)
              SET a.name = {attrname},
                  a.datatype = {datatype},
                  a.created_at = {date}
        ', [
            'username' => $this->user->getUsername(),
            'schemaname' => $schema->getName(),
            'attrname' => $attr->getName(),
            'datatype' => $attr->getDataType(),
            'date' => date(\DateTime::ISO8601)
        ]);
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
