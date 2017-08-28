<?php

namespace AppBundle\DomainCommand;


use AppBundle\Entity\Instance;
use AppBundle\Entity\Schema;
use AppBundle\Entity\User;

class CreateInstance extends DomainCommand
{

    public function execute(Instance $instance,
                            array $properties,
                            Schema $schema,
                            User $user): CreateInstance
    {
        foreach ($properties as $prop) {
            $this->em->persist($prop);
        }

        $this->em->persist(
            $instance
                ->setSchema($schema)
                ->setCreatedBy($user));
        return $this;
    }

}
