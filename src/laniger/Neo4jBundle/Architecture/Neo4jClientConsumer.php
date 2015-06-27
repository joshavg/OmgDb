<?php
namespace laniger\Neo4jBundle\Architecture;

trait Neo4jClientConsumer
{

    /**
     * @var Neo4jClientWrapper
     */
    private $client;

    public function __construct(Neo4jClientWrapper $client)
    {
        $this->client = $client;
    }
}