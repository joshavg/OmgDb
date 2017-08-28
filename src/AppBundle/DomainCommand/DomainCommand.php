<?php

namespace AppBundle\DomainCommand;


use Doctrine\ORM\EntityManagerInterface;

abstract class DomainCommand
{

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function flush()
    {
        $this->em->flush();
    }

}
