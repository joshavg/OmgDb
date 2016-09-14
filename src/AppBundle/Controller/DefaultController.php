<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    use RepositoryServices;

    /**
     * @Route("/", name="index")
     * @Template
     */
    public function indexAction()
    {
        $starred = $this->getInstanceRepository()->fetchStarredForCurrentUser();

        return [
            'starred' => $starred
        ];
    }
}
