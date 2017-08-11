<?php

namespace AppBundle\DomainCommand;


use AppBundle\Entity\Instance;
use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;

class AddTagToInstance
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
        if (!$instance->hasTag($tag)) {
            $instance
                ->addTag($tag)
                ->setUpdatedAt(new \DateTime());

            $this->em->persist($instance);
            $this->em->flush();
        }
    }

}
