<?php
namespace laniger\Neo4jBundle\Architecture;

use GraphAware\Neo4j\Client\Client;
use GraphAware\Neo4j\Client\ClientBuilder;
use GraphAware\Neo4j\Client\Formatter\Response;
use Psr\Log\LoggerInterface;

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
        ClientBuilder::create()->addConnection('default', '');
        $this->client =
            ClientBuilder::create()->addConnection('default', "http://$user:$pw@$host:$port")
                ->build();
    }

    /**
     * @param $cypher
     * @param array $parms
     * @return \GraphAware\Common\Result\Result
     */
    public function cypher($cypher, array $parms = [])
    {
        $this->logger->debug(__CLASS__ . ': executing cypher', [
            'query' => $cypher,
            'params' => $parms
        ]);

        return $this->client->run($cypher, $parms);
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
