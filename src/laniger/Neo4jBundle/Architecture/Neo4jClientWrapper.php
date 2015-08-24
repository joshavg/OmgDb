<?php
namespace laniger\Neo4jBundle\Architecture;

use Everyman\Neo4j\Client;
use Everyman\Neo4j\Cypher\Query;
use Psr\Log\LoggerInterface;

class Neo4jClientWrapper extends Client
{

    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    public function __construct($host, $pw, LoggerInterface $logger)
    {
        parent::__construct($host, $pw);
        $this->logger = $logger;
    }

    public function configure($user, $pw)
    {
        $this->getTransport()->setAuth($user, $pw);
    }

    public function cypher($cypher, array $parms = [])
    {
        $this->logger->debug(__CLASS__ . ': executing cypher', [
            'query' => $cypher,
            'params' => $parms
        ]);
        $qry = new Query($this, $cypher, $parms);
        return $qry->getResultSet();
    }
}