<?php

namespace AppBundle\DomainCommand;


use AppBundle\Entity\Instance;
use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;

class RemoveTagFromInstance
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function execute(Instance $instance, Tag $tag)
    {
        $instance->removeTag($tag);
        $this->em->persist($instance);
        $this->em->flush();
    }

}
