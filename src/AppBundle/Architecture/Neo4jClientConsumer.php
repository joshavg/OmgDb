<?php
namespace AppBundle\Architecture;

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