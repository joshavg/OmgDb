<?php
namespace laniger\Neo4jBundle\Architecture;

class Neo4jRepository
{

    /**
     * @var Neo4jClientWrapper
     */
    private $client;

    /**
     * Neo4jRepository constructor.
     * @param Neo4jClientWrapper $client
     */
    public function __construct(Neo4jClientWrapper $client)
    {
        $this->client = $client;
    }

    /**
     * @return Neo4jClientWrapper
     */
    public function getClient()
    {
        return $this->client;
    }
}
