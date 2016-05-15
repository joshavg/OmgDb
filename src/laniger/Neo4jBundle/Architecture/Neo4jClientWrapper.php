<?php
namespace laniger\Neo4jBundle\Architecture;

use GraphAware\Neo4j\Client\ClientBuilder;
use Psr\Log\LoggerInterface;

// TODO: finish transition to GraphAware
class Neo4jClientWrapper
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Client
     */
    private $client;

    public function __construct($host, $port, $user, $pw, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->client = ClientBuilder::create()->addConnection('default', 'http', $host, $port, true, $user, $pw)
            ->build();
    }

    /**
     * @param $cypher
     * @param array $parms
     * @return \Neoxygen\NeoClient\Request\Response
     */
    public function cypher($cypher, array $parms = [])
    {
        $this->logger->debug(__CLASS__ . ': executing cypher', [
            'query' => $cypher,
            'params' => $parms
        ]);

        return $this->client->sendCypherQuery($cypher, $parms);
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
