<?php

namespace AppBundle\DomainCommand;


use AppBundle\Entity\Instance;
use AppBundle\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;

class DeleteInstance
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function execute(Instance $instance)
    {
        $props = $this->em->getRepository(Property::class)
            ->findFromInstance($instance);
        foreach ($props as $prop) {
            $this->em->remove($prop);
        }

        $this->em->remove($instance);
        $this->em->flush();
    }

}
