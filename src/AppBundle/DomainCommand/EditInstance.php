<?php

namespace AppBundle\DomainCommand;


use AppBundle\Entity\Instance;
use AppBundle\Entity\Property;

class EditInstance extends DomainCommand
{

    /**
     * @param Instance $instance
     * @param Property[] $properties
     */
    public function execute(Instance $instance, array $properties)
    {
        $instance->setUpdatedAt(new \DateTime());
        $this->em->persist($instance);

        foreach ($properties as $prop) {
            $this->em->persist($prop);
        }
    }

}
