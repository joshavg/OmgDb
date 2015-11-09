<?php

namespace laniger\Neo4jBundle\Architecture;

/**
 * Class Neo4jClientConsumer
 * @package laniger\Neo4jBundle\Architecture
 */
trait Neo4jClientConsumer
{
    /**
     * @var Neo4jClientWrapper
     */
    private $client;

    /**
     * Neo4jClientConsumer constructor.
     * @param Neo4jClientWrapper $client
     */
    public function __construct(Neo4jClientWrapper $client)
    {
        $this->client = $client;
    }
}
