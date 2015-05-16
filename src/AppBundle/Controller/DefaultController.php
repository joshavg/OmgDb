<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Architecture\ContainerServices;

class DefaultController extends Controller
{
    use ContainerServices;

    /**
     * @Route("/", name="index")
     * @Template
     */
    public function indexAction()
    {
        return [];
    }
}
