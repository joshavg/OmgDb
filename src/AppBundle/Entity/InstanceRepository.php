<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\Client\Formatter\Type\Node;
use laniger\Neo4jBundle\Architecture\Neo4jClientWrapper;
use laniger\Neo4jBundle\Architecture\Neo4jRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class InstanceRepository extends Neo4jRepository
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

    public function newInstance(Instance $inst)
    {
        $trans = $this->getClient()->getClient()->transaction();

        $createdAt = date(\DateTime::ISO8601);
        $trans->push('
            MATCH (u:user)<-[:created_by]-(s:schema)
            WHERE u.name = {username}
              AND s.uid = {schemauid}
           CREATE (i:instance)-[:created_by]->(u),
                  (i)-[:instance_of]->(s)
              SET i.name = {name},
                  i.uid = {uid},
                  i.created_at = {date}
        ', [
            'username' => $this->user->getUsername(),
            'schemauid' => $inst->getSchema()->getUid(),
            'uid' => $inst->getUid(),
            'name' => $inst->getName(),
            'date' => $createdAt
        ]);

        foreach ($inst->getProperties() as $prop) {
            $trans->push('
                MATCH (i:instance)-[:created_by]->(u:user),
                      (a:attribute)-[:created_by]->(u)
                WHERE u.name = {username}
                  AND i.uid = {instanceuid}
                  AND a.uid = {attributeuid}
               CREATE (p:property)-[:created_by]->(u),
                      (p)-[:property_of]->(i),
                      (p)-[:instance_of]->(a)
                  SET p.created_at = {date},
                      p.value = {value},
                      p.uid = {propertyuid}
            ', [
                'username' => $this->user->getUsername(),
                'instanceuid' => $inst->getUid(),
                'attributeuid' => $prop->getAttributeUid(),
                'date' => $createdAt,
                'value' => $prop->getValue(),
                'propertyuid' => $prop->getUid()
            ]);
        }

        $trans->commit();
    }

    public function fetchAllForSchema(Schema $schema)
    {
        $ins = $this->getClient()->cypher('
            MATCH (i:instance)-[:instance_of]->(s:schema),
                  (i)<-[:property_of]-(p:property),
                  (p)-[:instance_of]->(a:attribute)
            WHERE s.uid = {schemauid}
           RETURN i, p, a
            ORDER bY i.created_at , a.order
        ', [
            'username' => $this->user->getUsername(),
            'schemauid' => $schema->getUid()
        ])->records();

        /** @var Instance[] */
        $instancemap = [];

        if (count($ins)) {
            foreach ($ins as $row) {
                $iuid = $row->get('i')->get('uid');

                if (!isset($instancemap[$iuid])) {
                    $instance = $this->createInstanceFromRow($row->get('i'));
                    $instance->setSchema($schema);
                    $instancemap[$iuid] = $instance;
                }

                $prop = $this->createPropertyFromRow($row->get('p'), $row->get('a'));
                $instancemap[$iuid]->addProperty($prop);
            }
        }

        return array_values($instancemap);
    }

    /**
     * @param Node $proprow
     * @param Node $attrnode
     * @return Property
     */
    private function createPropertyFromRow(Node $proprow, Node $attrnode)
    {
        $p = new Property();
        $p->setUid($proprow->get('uid'));
        $p->setValue($proprow->get('value'));
        $p->setAttributeUid($attrnode->get('uid'));

        $date = $proprow->get('created_at');
        $date = \DateTime::createFromFormat(\DateTime::ISO8601, $date);
        $p->setCreatedAt($date);

        return $p;
    }

    /**
     * @param Node $row
     * @return Instance
     */
    private function createInstanceFromRow(Node $row)
    {
        $i = new Instance();
        $i->setName($row->get('name'));
        $i->setUid($row->get('uid'));
        $i->setCreatedAt(\DateTime::createFromFormat(\DateTime::ISO8601, $row->get('created_at')));
        return $i;
    }

    public function fetchByUid($uid)
    {
        $row = $this->getClient()->cypher('
            MATCH (a:attribute)-[:attribute_of]->(s:schema),
                  (s)-[:created_by]->(u:user)
            WHERE u.name = {username}
              AND a.uid = {uid}
           RETURN a, s
        ', [
            'username' => $this->user->getUsername(),
            'uid' => $uid
        ])->firstRecord();

        $attr = $this->createInstanceFromRow($row->get('a'));

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
                  a.datatype = {newdatatype},
                  a.order = {order}
        ', [
            'schemaname' => $attr->getSchemaName(),
            'username' => $this->user->getUsername(),
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
