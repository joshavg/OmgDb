<?php
namespace AppBundle\Controller;


use AppBundle\Entity\Repository\AttributeRepository;
use AppBundle\Entity\Repository\InstanceRepository;
use AppBundle\Entity\Repository\RelationshipRepository;
use AppBundle\Entity\Repository\SchemaRepository;
use AppBundle\Entity\Repository\UserRepository;

trait RepositoryServices
{

    /**
     * @return UserRepository
     */
    private function getUserRepository()
    {
        return $this->container->get('repo.user');
    }

    /**
     * @return SchemaRepository
     */
    private function getSchemaRepository()
    {
        return $this->container->get('repo.schema');
    }

    /**
     * @return AttributeRepository
     */
    private function getAttributeRepository()
    {
        return $this->container->get('repo.attribute');
    }

    /**
     * @return InstanceRepository
     */
    private function getInstanceRepository()
    {
        return $this->container->get('repo.instance');
    }

    /**
     * @return RelationshipRepository
     */
    private function getRelationshipRepository()
    {
        return $this->container->get('repo.relationship');
    }
}
