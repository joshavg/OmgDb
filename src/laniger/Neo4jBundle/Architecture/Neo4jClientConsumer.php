<?php
namespace laniger\Neo4jBundle\Architecture;

trait Neo4jClientConsumer
{

    /**
     *
     * @var Neo4jClientWrapper
     */
    protected $client;

    public function setClient(Neo4jClientWrapper $client)
    {
        $this->client = $client;
    }
}