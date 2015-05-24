<?php
namespace AppBundle\Architecture;

use AppBundle\Entity\UserRepository;

trait RepositoryServices
{

    /**
     *
     * @return UserRepository
     */
    private function getUserRepository()
    {
        return $this->container->get('user.repo');
    }
}