<?php

namespace AppBundle\DomainCommand;


use AppBundle\Entity\Instance;
use AppBundle\Entity\Property;

class DeleteInstance extends DomainCommand
{

    public function execute(Instance $instance): DeleteInstance
    {
        $props = $this->em->getRepository(Property::class)
            ->findFromInstance($instance);
        foreach ($props as $prop) {
            $this->em->remove($prop);
        }

        $this->em->remove($instance);

        return $this;
    }

}
