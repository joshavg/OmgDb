<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/app/example", name="homepage")
     */
    public function indexAction()
    {
        $em = $this->container->get('neo4j.manager');
        var_dump($em->cypherQuery('MATCH (n) RETURN n'));
        $repo = $em->getRepository('AppBundle\\Entity\\User');
        $john = $repo->findOneBy([
            'name' => 'admin'
        ]);
        var_dump($john);
    
        return $this->render('default/index.html.twig');
    }
}
