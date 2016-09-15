<?php
namespace AppBundle\Architecture;

use laniger\Neo4jBundle\Architecture\Neo4jClientWrapper;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * wrapper for container service getters
 */
trait ContainerServices
{
    use ContainerAwareTrait;

    /**
     * @return Neo4jClientWrapper
     */
    private function getNeo4jClient()
    {
        return $this->container->get('neo4j.client');
    }

    /**
     * @return EncoderFactoryInterface
     */
    private function getPasswordEncoderFactory()
    {
        return $this->container->get('security.encoder_factory');
    }

    /**
     * @return FlashbagHandler
     */
    private function getFlashbagHandler()
    {
        return $this->container->get('handler.flashbag');
    }
}
