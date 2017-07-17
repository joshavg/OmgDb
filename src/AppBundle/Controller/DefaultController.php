<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template
     */
    public function indexAction()
    {
        $instances = $this->getDoctrine()
            ->getRepository('AppBundle:Instance')
            ->createQueryBuilder('i')
            ->andWhere('i.createdBy = :u')
            ->orderBy('i.createdAt')
            ->setMaxResults(10)
            ->setParameters([
                'u' => $this->getUser()
            ])
            ->getQuery()
            ->execute();

        return [
            'instances' => $instances
        ];
    }
}
