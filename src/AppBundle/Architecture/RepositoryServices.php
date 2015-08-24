<?php
namespace AppBundle\Architecture;

use AppBundle\Entity\UserRepository;
use AppBundle\Entity\SchemaRepository;
use AppBundle\Entity\AttributeRepository;

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
}