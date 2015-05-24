<?php
namespace laniger\Neo4jBundle\Architecture;

use Everyman\Neo4j\Client;
use Everyman\Neo4j\Cypher\Query;

class Neo4jClientWrapper extends Client
{

    public function configure($user, $pw)
    {
        $this->getTransport()->setAuth($user, $pw);
    }

    public function cypher($cypher, array $parms = []) {
        $qry = new Query($this, $cypher, $parms);
        return $qry->getResultSet();
    }
}