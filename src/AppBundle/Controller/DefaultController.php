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
            ->findLatestFromUser($this->getUser());

        $tags = $this->getDoctrine()
            ->getRepository('AppBundle:Tag')
            ->findLatestUsed($this->getUser(), 5);

        return [
            'instances' => $instances,
            'tags' => $tags
        ];
    }
}
