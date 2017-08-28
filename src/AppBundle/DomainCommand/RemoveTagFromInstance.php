<?php

namespace AppBundle\DomainCommand;


use AppBundle\Entity\Instance;
use AppBundle\Entity\Tag;

class RemoveTagFromInstance extends DomainCommand
{

    public function execute(Instance $instance, Tag $tag): RemoveTagFromInstance
    {
        $instance->removeTag($tag);
        $this->em->persist($instance);
        return $this;
    }

}
