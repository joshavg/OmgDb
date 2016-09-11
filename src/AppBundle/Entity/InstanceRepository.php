<?php
namespace AppBundle\Entity;

use AppBundle\Architecture\DateFactory;
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

    /**
     * @var AttributeRepository
     */
    private $attrrepo;

    /**
     * @var DateFactory
     */
    private $dateFactory;

    public function __construct(Neo4jClientWrapper $client, TokenStorage $storage,
                                AttributeRepository $attrrepo, DateFactory $dateFactory)
    {
        parent::__construct($client);
        $this->user = $storage->getToken()->getUser();
        $this->attrrepo = $attrrepo;
        $this->dateFactory = $dateFactory;
    }

    public function newInstance(Instance $inst)
    {
        $trans = $this->getClient()->getClient()->transaction();

        $createdAt = $this->dateFactory->nowString();
        $trans->push('
            MATCH (u:user)<-[:created_by]-(s:schema)
            WHERE u.name = {username}
              AND s.uid = {schemauid}
           CREATE (i:instance)-[:created_by]->(u),
                  (i)-[:instance_of]->(s)
              SET i.name = {name},
                  i.uid = {uid},
                  i.created_at = {date},
                  i.updated_at = {updated}
        ', [
            'username' => $this->user->getUsername(),
            'schemauid' => $inst->getSchemaUid(),
            'uid' => $inst->getUid(),
            'name' => $inst->getName(),
            'date' => $createdAt,
            'updated' => $createdAt
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

    public function fetchAllForSchema($schemauid)
    {
        $ins = $this->getClient()->cypher('
            MATCH (i:instance)-[:instance_of]->(s:schema),
                  (p:property)-[:property_of]->(i),
                  (p)-[:instance_of]->(a:attribute)
            WHERE s.uid = {schemauid}
           RETURN i, p, a
            ORDER BY i.created_at DESC, a.order
        ', [
            'username' => $this->user->getUsername(),
            'schemauid' => $schemauid
        ])->records();

        /** @var Instance[] */
        $instancemap = [];

        if (count($ins)) {
            foreach ($ins as $row) {
                $iuid = $row->get('i')->get('uid');

                if (!isset($instancemap[$iuid])) {
                    $instance = $this->createInstanceFromRow($row->get('i'));
                    $instance->setSchemaUid($schemauid);
                    $instance->setCreatedBy($this->user->getUsername());
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
     * @param Node $attrrow
     * @return Property
     */
    private function createPropertyFromRow(Node $proprow, Node $attrrow)
    {
        $p = new Property();

        if ($proprow->containsKey('value')) {
            $p->setValue($proprow->get('value'));
        }

        $p->setUid($proprow->get('uid'));
        $p->setAttributeUid($attrrow->get('uid'));
        $p->setAttribute($this->attrrepo->createFromRow($attrrow));

        $date = $proprow->get('created_at');
        $date = $this->dateFactory->fromString($date);
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
        $i->setCreatedAt($this->dateFactory->fromString($row->get('created_at')));
        $i->setUpdatedAt($this->dateFactory->fromString($row->get('updated_at')));
        return $i;
    }

    public function fetchByUid($uid)
    {
        $rows = $this->getClient()->cypher('
            MATCH (i:instance)<-[:property_of]-(p:property),
                  (i)-[:instance_of]->(s:schema),
                  (p)-[:instance_of]->(a:attribute)
            WHERE i.uid = {uid}
           RETURN i, s, p, a
            ORDER BY a.order
        ', [
            'uid' => $uid
        ])->records();

        $i = null;
        if (count($rows)) {
            $i = $this->createInstanceFromRow($rows[0]->get('i'));
            $i->setSchemaUid($rows[0]->get('s')->get('uid'));
            $i->setCreatedBy($this->user->getUsername());
            foreach ($rows as $row) {
                $i->addProperty($this->createPropertyFromRow($row->get('p'), $row->get('a')));
            }
        }

        return $i;
    }

    public function update(Instance $instance)
    {
        $trans = $this->getClient()->getClient()->transaction();

        $updated = $this->dateFactory->nowString();
        $instance->setUpdatedAt($this->dateFactory->fromString($updated));
        $trans->push('
            MATCH (i:instance)
            WHERE i.uid = {uid}
              SET i.name = {newname},
                  i.updated_at = {updated}
        ', [
            'uid' => $instance->getUid(),
            'newname' => $instance->getName(),
            'updated' => $updated
        ]);

        foreach ($instance->getProperties() as $prop) {
            $trans->push('
                MATCH (p:property)
                WHERE p.uid = {uid}
                  SET p.value = {newvalue}
            ', [
                'uid' => $prop->getUid(),
                'newvalue' => $prop->getValue()
            ]);
        }

        $trans->commit();
    }

    public function deleteByUid($uid)
    {
        $this->getClient()->cypher('
            MATCH (i:instance)-[ir]-(),
                  (p:property)-[pr:property_of]->(i),
                  (p)-[r2]-()
            WHERE i.uid = {uid}
            DELETE r2, pr, p, ir, i
        ', [
            'uid' => $uid
        ]);
    }
}
