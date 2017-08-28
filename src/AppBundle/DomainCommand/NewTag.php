<?php

namespace AppBundle\DomainCommand;


use AppBundle\Entity\Tag;
use AppBundle\Entity\User;

class NewTag extends DomainCommand
{

    public function execute(Tag $tag, User $user): NewTag
    {
        $this->em->persist(
            $tag->setCreatedBy($user));
        return $this;
    }

}
