<?php
namespace AppBundle\Architecture;

/**
 * wrapper for container service getters
 *
 * @author laniger
 */
trait ContainerServices
{

    /**
     * @return Neo4jClientWrapper
     */
    private function getNeo4jClient() {
        return $this->container->get('neo4j_client');
    }
}