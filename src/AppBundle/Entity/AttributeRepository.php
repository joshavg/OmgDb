<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\Client\Formatter\Type\Node;
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
        ])->records();

        $attributes = [];

        if (count($attr)) {
            foreach ($attr as $row) {
                $a = $this->createFromRow($row->get('a'));
                $a->setSchemaName($schema->getName());
                $a->setSchemaUid($schema->getUid());
                $attributes[] = $a;
            }
        }
        return $attributes;
    }

    public function newAttribute(Attribute $attr)
    {
        $this->getClient()->cypher('
            MATCH (u:user)<-[:created_by]-(s:schema)
            WHERE u.name = {username}
              AND s.name = {schemaname}
           CREATE (a:attribute)-[:attribute_of]->(s)
              SET a.name = {attrname},
                  a.datatype = {datatype},
                  a.created_at = {date},
                  a.uid = {uid}
        ', [
            'username' => $this->user->getUsername(),
            'schemaname' => $attr->getSchemaName(),
            'attrname' => $attr->getName(),
            'datatype' => $attr->getDataType()->getName(),
            'date' => date(\DateTime::ISO8601),
            'uid' => $attr->getUid()
        ]);
    }

    public function isNameUniqueForCurrentUser(Attribute $attr)
    {
        $count = $this->getClient()->cypher('
            MATCH (a:attribute)-[:attribute_of]->(s:schema),
                  (s)-[:created_by]->(u:user)
            WHERE s.uid = {schemaUid}
              AND u.name = {username}
              AND a.name = {attributeName}
           RETURN COUNT(a) AS cnt
        ', [
            'schemaUid' => $attr->getSchemaUid(),
            'username' => $this->user->getUsername(),
            'attributeName' => $attr->getName()
        ])->firstRecord()->get('cnt');

        return $count < 1;
    }

    private function createFromRow(Node $row)
    {
        $a = new Attribute();
        $a->setName($row->get('name'));
        $a->setCreatedAt(\DateTime::createFromFormat(\DateTime::ISO8601,
            $row->get('created_at')));
        $a->setDataType(AttributeDataType::getByName($row->get('datatype')));
        $a->setUid($row->get('uid'));
        return $a;
    }

    public function fetchByUid($schemaUid, $uid)
    {
        $row = $this->getClient()->cypher('
            MATCH (a:attribute)-[:attribute_of]->(s:schema),
                  (s)-[:created_by]->(u:user)
            WHERE s.uid = {schemaUid}
              AND u.name = {username}
              AND a.uid = {attributeUid}
           RETURN a, s
        ', [
            'schemaUid' => $schemaUid,
            'username' => $this->user->getUsername(),
            'attributeUid' => $uid
        ])->firstRecord();

        $attr = $this->createFromRow($row->get('a'));

        $schema = $row->get('s');
        $attr->setSchemaName($schema->get('name'));
        $attr->setSchemaUid($schema->get('uid'));

        return $attr;
    }

    public function update($uid, Attribute $attr)
    {
        $this->getClient()->cypher('
            MATCH (a:attribute)-[:attribute_of]->(s:schema),
                  (s)-[:created_by]->(u:user)
            WHERE s.name = {schemaname}
              AND u.name = {username}
              AND a.uid = {attributeUid}
              SET a.name = {newname},
                  a.datatype = {newdatatype}
        ', [
            'schemaname' => $attr->getSchemaName(),
            'username' => $this->user->getUsername(),
            'attributeUid' => $uid,
            'newname' => $attr->getName(),
            'newdatatype' => $attr->getDataType()->getName()
        ]);
    }
}
