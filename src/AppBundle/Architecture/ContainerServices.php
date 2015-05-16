<?php
namespace AppBundle\Architecture;

use HireVoice\Neo4j\EntityManager;

/**
 * wrapper for container service getters
 *
 * @author laniger
 */
trait ContainerServices
{

    /**
     * @return EntityManager
     */
    private function getNeo4jEm()
    {
        return $this->container->get('neo4j.manager');
    }

    /**
     * @return Neo4jClientWrapper
     */
    private function getNeo4jClient() {
        return $this->container->get('neo4j_client');
    }
}