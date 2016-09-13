<?php
namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Attribute;
use AppBundle\Entity\AttributeDataType;
use AppBundle\Entity\Schema;
use AppBundle\Entity\User;
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

    /**
     * @var DateFactory
     */
    private $dateFactory;

    public function __construct(Neo4jClientWrapper $client, TokenStorage $storage,
                                DateFactory $dateFactory)
    {
        parent::__construct($client);
        $this->user = $storage->getToken()->getUser();
        $this->dateFactory = $dateFactory;
    }

    /**
     * @param Schema $schema
     * @return Attribute[]
     */
    public function getForSchema(Schema $schema)
    {
        $attr = $this->getClient()->cypher('
            MATCH (s:schema)<-[:attribute_of]-(a:attribute)
            WHERE s.uid = {uid}
           RETURN a
            ORDER BY a.order, a.name
        ', [
            'uid' => $schema->getUid()
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
           CREATE (a:attribute)-[:attribute_of]->(s),
                  (a)-[:created_by]->(u)
              SET a.name = {attrname},
                  a.datatype = {datatype},
                  a.created_at = {date},
                  a.uid = {uid},
                  a.order = {order}
        ', [
            'username' => $this->user->getUsername(),
            'schemaname' => $attr->getSchemaName(),
            'attrname' => $attr->getName(),
            'datatype' => $attr->getDataType()->getName(),
            'date' => $this->dateFactory->nowString(),
            'uid' => $attr->getUid(),
            'order' => $attr->getOrder()
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

    public function createFromRow(Node $row)
    {
        $a = new Attribute();
        $a->setName($row->get('name'));
        $a->setCreatedAt($this->dateFactory->fromString($row->get('created_at')));
        $a->setDataType(AttributeDataType::getByName($row->get('datatype')));
        $a->setUid($row->get('uid'));
        $a->setOrder($row->get('order'));
        return $a;
    }

    public function fetchByUid($uid)
    {
        $row = $this->getClient()->cypher('
            MATCH (a:attribute)-[:attribute_of]->(s:schema)
            WHERE a.uid = {uid}
           RETURN a, s
        ', [
            'username' => $this->user->getUsername(),
            'uid' => $uid
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
            MATCH (a:attribute)
            WHERE a.uid = {attributeUid}
              SET a.name = {newname},
                  a.datatype = {newdatatype},
                  a.order = {order}
        ', [
            'attributeUid' => $uid,
            'newname' => $attr->getName(),
            'newdatatype' => $attr->getDataType()->getName(),
            'order' => $attr->getOrder()
        ]);
    }

    public function deleteByUid($uid)
    {
        $this->getClient()->cypher('
            MATCH (a:attribute)-[r:attribute_of]->(s:schema),
                  (s)-[:created_by]->(u:user)
            WHERE a.uid = {uid}
              AND u.name = {username}
            DELETE r, a
        ', [
            'uid' => $uid,
            'username' => $this->user->getUsername()
        ]);
    }
}
