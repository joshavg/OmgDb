<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\Client\Formatter\Type\Node;
use laniger\Neo4jBundle\Architecture\Neo4jClientWrapper;
use laniger\Neo4jBundle\Architecture\Neo4jRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class RelationshipRepository extends Neo4jRepository
{

    /**
     * @var InstanceRepository
     */
    private $instanceRepo;

    public function __construct(Neo4jClientWrapper $client, InstanceRepository $instanceRepo)
    {
        parent::__construct($client);
        $this->instanceRepo = $instanceRepo;
    }

    public function getRelations(Instance $instance)
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
            'to' => [],
            'from' => []
        ];

        if (count($rows)) {
            foreach ($rows as $row) {
                $i2uid = $row->get('i2')->get('uid');
                $instance2 = $this->instanceRepo->fetchByUid($i2uid);

                $relCreated = $row->get('r')->get('created_at');
                $rel = new Relationship();
                $rel->setCreatedAt(\DateTime::createFromFormat(\DateTime::ISO8601, $relCreated));

                $fromUid = $row->get('fromnod')->get('uid');
                if ($fromUid === $instance->getUid()) {
                    $rel->setFrom($instance);
                    $rel->setTo($instance2);
                    $relations['from'][] = $rel;
                } else {
                    $rel->setTo($instance);
                    $rel->setFrom($instance2);
                    $relations['to'][] = $rel;
                }
            }
        }

        var_dump($relations, $rows);
        return $relations;
    }

}
