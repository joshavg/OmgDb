<?php
namespace AppBundle\Entity;

use AppBundle\Architecture\DateFactory;
use laniger\Neo4jBundle\Architecture\Neo4jClientWrapper;
use laniger\Neo4jBundle\Architecture\Neo4jRepository;
use GraphAware\Neo4j\Client\Formatter\Type\Relationship as Neo4jRelationship;

class RelationshipRepository extends Neo4jRepository
{

    /**
     * @var InstanceRepository
     */
    private $instanceRepo;

    /**
     * @var DateFactory
     */
    private $dateFactory;

    public function __construct(Neo4jClientWrapper $client, InstanceRepository $instanceRepo,
                                DateFactory $dateFactory)
    {
        parent::__construct($client);
        $this->instanceRepo = $instanceRepo;
        $this->dateFactory = $dateFactory;
    }

    public function fetchByUid($uid)
    {
        $row = $this->getClient()->cypher('
            MATCH (i1:instance)-[r:related_to]-(i2:instance)
            WHERE r.uid = {uid}
           RETURN i1.uid AS i1uid,
                  i2.uid AS i2uid,
                  startnode(r).uid AS fromuid,
                  r
        ', [
            'uid' => $uid
        ])->firstRecord();

        if ($row) {
            $i1uid = $row->get('i1uid');
            $i2uid = $row->get('i2uid');
            $fromuid = $row->get('fromuid');

            $instance1 = $this->instanceRepo->fetchByUid($i1uid);
            $instance2 = $this->instanceRepo->fetchByUid($i2uid);

            $rel = $this->createFromRow($row->get('r'));
            if ($fromuid === $i1uid) {
                $rel->setFrom($instance1)->setTo($instance2);
            } else {
                $rel->setFrom($instance2)->setTo($instance1);
            }

            return $rel;
        }

        return null;
    }

    public function getRelationships(Instance $instance)
    {
        $rows = $this->getClient()->cypher('
            MATCH (i:instance)-[r:related_to]-(i2:instance)
            WHERE i.uid = {uid}
           RETURN i, i2, r, startnode(r) AS fromnode
            ORDER BY r.created_at DESC
        ', [
            'uid' => $instance->getUid()
        ])->records();

        $relations = [
            'outgoing' => [],
            'incoming' => []
        ];

        if (count($rows)) {
            foreach ($rows as $row) {
                $i2uid = $row->get('i2')->get('uid');
                $instance2 = $this->instanceRepo->fetchByUid($i2uid);

                $relData = $row->get('r');
                $rel = $this->createFromRow($relData);

                $fromUid = $row->get('fromnode')->get('uid');
                if ($fromUid === $instance->getUid()) {
                    $rel->setFrom($instance)->setTo($instance2);
                    $relations['outgoing'][] = $rel;
                } else {
                    $rel->setTo($instance)->setFrom($instance2);
                    $relations['incoming'][] = $rel;
                }
            }
        }

        return $relations;
    }

    public function deleteRelationship($uid)
    {
        $this->getClient()->cypher('
            MATCH (:instance)-[r:related_to]-(:instance)
            WHERE r.uid = {uid}
           DELETE r
        ', [
            'uid' => $uid
        ]);
    }

    /**
     * @param Neo4jRelationship $row
     * @return Relationship
     */
    private function createFromRow(Neo4jRelationship $row)
    {
        $rel = new Relationship();
        $rel->setCreatedAt($this->dateFactory->fromString($row->get('created_at')));
        $rel->setLabel($row->get('label'));
        $rel->setUid($row->get('uid'));

        return $rel;
    }

    public function createRelationships($fromInstanceUid, $label, array $toInstanceUids)
    {
        $trans = $this->getClient()->getClient()->transaction();

        // TODO can this be done in one query?
        foreach ($toInstanceUids as $to) {
            $trans->push('
                MATCH (from:instance),
                      (to:instance)
                WHERE from.uid = {from}
                  AND to.uid = {to}
               CREATE (from)-[r:related_to]->(to)
                  SET r.label = {label},
                      r.uid = {uid},
                      r.created_at = {created_at}
            ', [
                'from' => $fromInstanceUid,
                'to' => $to,
                'label' => $label,
                'uid' => (new Relationship())->getUid(),
                'created_at' => $this->dateFactory->nowString()
            ]);
        }

        $trans->commit();
    }

}
