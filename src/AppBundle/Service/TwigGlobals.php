<?php

namespace AppBundle\Service;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TwigGlobals
{

    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(TokenStorageInterface $storage, ManagerRegistry $registry)
    {
        $this->storage = $storage;
        $this->registry = $registry;
    }

    public function createdSchemas()
    {
        /** @var $qb QueryBuilder */
        $qb = $this->registry
            ->getRepository('AppBundle:Schema')
            ->createQueryBuilder('s');

        return
            $qb->where('s.createdBy = :u OR s.public = :p')
                ->orderBy('s.name')
                ->setParameters([
                    'u' => $this->storage->getToken()->getUser(),
                    'p' => true
                ])
                ->getQuery()
                ->execute();
    }

}
