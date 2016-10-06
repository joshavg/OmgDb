<?php
namespace AppBundle\Entity\Repository;

use AppBundle\Entity\AttributeDataType;
use AppBundle\Entity\Instance;
use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyTransformer\PropertyTransformerRepository;
use AppBundle\Entity\User;
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

    /**
     * @var PropertyTransformerRepository
     */
    private $transformerRepo;

    public function __construct(Neo4jClientWrapper $client, TokenStorage $storage,
                                AttributeRepository $attrrepo, DateFactory $dateFactory,
                                PropertyTransformerRepository $transformerRepo)
    {
        parent::__construct($client);

        $this->user = $storage->getToken()->getUser();
        $this->attrrepo = $attrrepo;
        $this->dateFactory = $dateFactory;
        $this->transformerRepo = $transformerRepo;
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
           RETURN s, i, p, a
            ORDER BY i.created_at DESC, a.order
        ', [
            'username' => $this->user->getUsername(),
            'schemauid' => $schemauid
        ])->records();

        if (count($ins)) {
            return $this->createInstancesFromResult($ins);
        }

        return null;
    }

    /**
     * @param $ins
     * @return array
     */
    private function createInstancesFromResult(array $ins)
    {
        $instancemap = [];

        foreach ($ins as $row) {
            /** @var Node $row */
            $iuid = $row->get('i')->get('uid');

            if (!isset($instancemap[$iuid])) {
                $instance = $this->createInstanceFromRow($row->get('i'));
                $instance->setSchemaUid($row->get('s')->get('uid'));
                $instance->setCreatedBy($this->user->getUsername());
                $instancemap[$iuid] = $instance;
            }

            $prop = $this->createPropertyFromRow($row->get('a'), $row->get('p'));
            $instancemap[$iuid]->addProperty($prop);
        }

        return array_values($instancemap);
    }

    public function fetchStarredForCurrentUser()
    {
        $rows = $this->getClient()->cypher('
            MATCH (i:instance)-[:created_by]->(u:user),
                  (i)-[:instance_of]->(s:schema),
                  (p:property)-[:property_of]->(i),
                  (p)-[:instance_of]->(a:attribute)
            WHERE u.name = {user}
              AND i.starred = true
           RETURN s, i, p, a
            ORDER BY i.updated_at DESC
        ', [
            'user' => $this->user->getUsername()
        ])->records();

        if (count($rows)) {
            return $this->createInstancesFromResult($rows);
        }

        return null;
    }

    /**
     * @param Node $proprow
     * @param Node $attrrow
     * @return Property
     */
    private function createPropertyFromRow(Node $attrrow, Node $proprow = null)
    {
        $p = new Property();

        $p->setAttributeUid($attrrow->get('uid'));
        $p->setAttribute($this->attrrepo->createFromRow($attrrow));

        if ($proprow) {
            if ($proprow->containsKey('value')) {
                $value = $proprow->get('value');

                $datatype = $p->getAttribute()->getDataType();
                $transformerName = $datatype->getTransformerName();
                $transformer = $this->transformerRepo->getTransformer($transformerName);
                $p->setValue($transformer->fromDatabaseToNormalForm($value));
            }
            $p->setUid($proprow->get('uid'));

            $date = $proprow->get('created_at');
            $date = $this->dateFactory->fromString($date);
            $p->setCreatedAt($date);
        }

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
        $i->setStarred($row->containsKey('starred') ? $row->get('starred') : false);
        return $i;
    }

    public function fetchByUid($uid)
    {
        $rows = $this->getClient()->cypher('
            MATCH (i:instance)-[:instance_of]->(s:schema),
                  (a:attribute)-[:attribute_of]->(s)
            WHERE i.uid = {uid}
         OPTIONAL MATCH
                  (p:property)-[:instance_of]->(a),
                  (p)-[:property_of]->(i)
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
                $i->addProperty($this->createPropertyFromRow($row->get('a'), $row->get('p')));
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
            MERGE (i:instance {uid: {uid}})
               ON CREATE SET
                  i.uid = {uid}
              SET i.name = {newname},
                  i.updated_at = {updated}
        ', [
            'uid' => $instance->getUid(),
            'newname' => $instance->getName(),
            'updated' => $updated
        ]);

        foreach ($instance->getProperties() as $prop) {
            $value = $prop->getValue();
            if ($value instanceof \DateTime) {
                $value = $this->dateFactory->toString($value);
            }

            $trans->push('
                MATCH (i:instance),
                      (u:user),
                      (a:attribute)
                WHERE i.uid = {instanceuid}
                  AND u.name = {username}
                  AND a.uid = {attributeuid}
                MERGE (p:property {uid: {uid}})-[:property_of]->(i)
                   ON CREATE SET
                      p.created_at = {created_at}
                MERGE (p)-[:created_by]->(u)
                MERGE (p)-[:instance_of]->(a)
                  SET p.value = {newvalue}
            ', [
                'uid' => $prop->getUid(),
                'instanceuid' => $instance->getUid(),
                'created_at' => $this->dateFactory->nowString(),
                'username' => $this->user->getUsername(),
                'attributeuid' => $prop->getAttributeUid(),
                'newvalue' => $value
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

    public function updateStarred($uid, $starred)
    {
        $this->getClient()->cypher('
            MATCH (i:instance)
            WHERE i.uid = {uid}
              SET i.starred = {starred}
        ', [
            'uid' => $uid,
            'starred' => $starred
        ]);
    }
}
